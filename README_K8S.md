# Laravel Kubernetes Deployment

This project has been configured for deployment on Kubernetes using Docker containers.

## Prerequisites

-   Kubernetes cluster
-   Helm installed
-   kubectl configured

## Docker Images

The Docker images have been built locally:

-   `frontend`: Nginx with built assets
-   `backend`: PHP-FPM with Laravel application

## Deployment Steps

1. **Install MySQL and Redis using Helm:**

    ```bash
    helm repo add bitnami https://charts.bitnami.com/bitnami
    helm repo update

    helm install mysql bitnami/mysql --set auth.database="application" --set auth.username="ahmed" --set auth.password="sqlhardPass123"

    helm install redis bitnami/redis --set auth.password="rdshardPass123"
    ```

2. **Apply Kubernetes manifests:**

    ```bash
    kubectl apply -f k8s/app_config.yaml
    kubectl apply -f k8s/app_secret.yaml
    kubectl apply -f k8s/app_deployment.yaml
    kubectl apply -f k8s/app_service.yaml
    ```

3. **Check deployment:**

    ```bash
    kubectl get pods
    kubectl get svc
    ```

The application should be accessible via the NodePort service on port 30007.

## Notes

-   The Laravel application is configured to use Redis for sessions and cache.
-   Database migrations are run automatically in the init container.
-   Secrets contain base64 encoded passwords; update them as needed for production.
