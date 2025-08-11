#!/bin/bash
set -e

IMAGE_NAME="fikridulumina/sih3-app:latest"

echo "=== Building lightweight Laravel production image: $IMAGE_NAME ==="
docker compose -f docker-compose.prod.yaml build

# echo "=== Pushing image to registry ==="
# docker push $IMAGE_NAME

# echo "=== Done! Image pushed successfully ==="
