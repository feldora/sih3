DROP PROCEDURE IF EXISTS insert_geo_feature_points;
CREATE PROCEDURE insert_geo_feature_points(IN feature_id BIGINT)
BEGIN
    DELETE FROM geo_feature_points WHERE id_geo_features = feature_id;

    DROP TEMPORARY TABLE IF EXISTS temp_seq;
    CREATE TEMPORARY TABLE temp_seq(n INT PRIMARY KEY);

    INSERT INTO temp_seq(n)
    WITH RECURSIVE seq(n) AS (
        SELECT 1
        UNION ALL
        SELECT n + 1 FROM seq WHERE n < 1000
    )
    SELECT n FROM seq;

    DROP TEMPORARY TABLE IF EXISTS temp_parts;
    CREATE TEMPORARY TABLE temp_parts(p INT PRIMARY KEY);

    INSERT INTO temp_parts(p)
    WITH RECURSIVE parts(p) AS (
        SELECT 1
        UNION ALL
        SELECT p + 1 FROM parts WHERE p <= 10
    )
    SELECT p FROM parts;

    INSERT INTO geo_feature_points (
        id_geo_features,
        longitude,
        latitude,
        point
    )
    SELECT 
        gf.id,
        ST_X(ST_PointN(ST_ExteriorRing(ST_GeometryN(gf.geom, temp_parts.p)), temp_seq.n)),
        ST_Y(ST_PointN(ST_ExteriorRing(ST_GeometryN(gf.geom, temp_parts.p)), temp_seq.n)),
        ST_PointFromText(
            CONCAT(
                'POINT(',
                ST_X(ST_PointN(ST_ExteriorRing(ST_GeometryN(gf.geom, temp_parts.p)), temp_seq.n)),
                ' ',
                ST_Y(ST_PointN(ST_ExteriorRing(ST_GeometryN(gf.geom, temp_parts.p)), temp_seq.n)),
                ')'
            )
        )
    FROM geo_features gf
    JOIN temp_parts ON temp_parts.p <= ST_NumGeometries(gf.geom)
    JOIN temp_seq ON temp_seq.n <= ST_NumPoints(ST_ExteriorRing(ST_GeometryN(gf.geom, temp_parts.p)))
    WHERE gf.id = feature_id AND gf.tag = 'kecamatan';

    DROP TEMPORARY TABLE IF EXISTS temp_seq;
    DROP TEMPORARY TABLE IF EXISTS temp_parts;
END;



DROP TRIGGER IF EXISTS after_insert_geo_features;
CREATE TRIGGER after_insert_geo_features
AFTER INSERT ON geo_features
FOR EACH ROW
BEGIN
    IF NEW.tag = 'kecamatan' THEN
        CALL insert_geo_feature_points(NEW.id);
    END IF;
END;

DROP TRIGGER IF EXISTS after_update_geo_features;
CREATE TRIGGER after_update_geo_features
AFTER UPDATE ON geo_features
FOR EACH ROW
BEGIN
    IF NEW.tag = 'kecamatan' THEN
        CALL insert_geo_feature_points(NEW.id);
    END IF;
END;
