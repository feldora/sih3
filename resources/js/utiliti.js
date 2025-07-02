
export function decimalToDMS(decimal) {
    const degrees = Math.floor(decimal);
    const minutesDecimal = Math.abs(decimal - degrees) * 60;
    const minutes = Math.floor(minutesDecimal);
    const seconds = Math.round((minutesDecimal - minutes) * 60);

    return `${degrees}° ${minutes}' ${seconds}"`;
}

export function DMSToDecimal(dms) {
    // Regex untuk memisahkan derajat, menit, detik dari string
    const regex = /^(-?\d+)° (\d+)' (\d+)"$/;
    const matches = dms.match(regex);
    
    if (matches) {
        const degrees = parseInt(matches[1]);
        const minutes = parseInt(matches[2]);
        const seconds = parseInt(matches[3]);
        
        const sign = degrees < 0 ? -1 : 1;
        const decimal = Math.abs(degrees) + minutes / 60 + seconds / 3600;
        return sign * decimal;
    } else {
        throw new Error("Format DMS tidak valid. Pastikan dalam format 'deg° min' sec\"'");
    }
}
