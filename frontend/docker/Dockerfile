# Stage 1: build the static bundle
FROM node:22-alpine AS build

WORKDIR /app

# Dependencies are installed from the lockfile only, so the image is reproducible
COPY package.json package-lock.json ./
RUN npm ci

# The API path is baked in at build time by Vite. The default keeps requests
# relative, which lets nginx proxy them to the api container.
ARG VITE_API_BASE_URL=/api/v1
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

COPY . .
RUN npm run build

# Stage 2: serve the bundle
FROM nginx:alpine AS runtime

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s \
    CMD wget --quiet --tries=1 --spider http://127.0.0.1/ || exit 1
