# Kubernetes Deployment für Trade Republic Investment Tracker

## Voraussetzungen

-   K3s Cluster mit Traefik
-   ArgoCD installiert
-   Sealed Secrets Controller installiert
-   PostgreSQL Datenbank (bereits konfiguriert)
-   kubectl und kubeseal CLI Tools

## Installation

### 1. SealedSecrets erstellen

Die Secrets müssen mit `kubeseal` verschlüsselt werden:

```bash
# APP_KEY verschlüsseln
echo -n 'base64:1QXubIemgtrZ/Qq/klKfyFvdBWOjuzCWKdTf36hrphA=' | \
  kubeseal --raw --from-file=/dev/stdin \
  --namespace=trade-republic-tracker \
  --name=trade-republic-secret \
  --scope=strict

# DB_PASSWORD verschlüsseln (Postgres Password)
echo -n 'dein-postgres-password' | \
  kubeseal --raw --from-file=/dev/stdin \
  --namespace=trade-republic-tracker \
  --name=trade-republic-secret \
  --scope=strict
```

Füge die verschlüsselten Werte in `sealed-secret.yaml` ein.

### 2. ConfigMap anpassen

Bearbeite `sealed-secret.yaml` (ConfigMap Teil):

-   `APP_URL`: Deine Domain
-   `DB_DATABASE`: Dein Datenbankname
-   `DB_USERNAME`: Dein DB-Benutzername
-   `MAIL_FROM_ADDRESS`: Deine E-Mail-Adresse

### 3. Ingress anpassen

Bearbeite `ingress.yaml`:

-   Ersetze `trade-republic.deine-domain.de` mit deiner Domain

### 4. Deployment anpassen

Bearbeite `deployment.yaml`:

-   Ersetze `ghcr.io/dein-github-user/trade-republic-tracker:latest` mit deinem Container Image

### 5. ArgoCD Application anpassen

Bearbeite `argocd-application.yaml`:

-   `repoURL`: Dein Git Repository
-   `targetRevision`: Dein Branch (main/master/production)

### 6. Docker Image bauen und pushen

```bash
# Image bauen
docker build -t ghcr.io/dein-github-user/trade-republic-tracker:latest .

# Image pushen
docker push ghcr.io/dein-github-user/trade-republic-tracker:latest
```

Oder nutze die GitHub Actions Pipeline (siehe `.github/workflows/deploy.yml`).

### 7. Mit ArgoCD deployen

```bash
# ArgoCD Application erstellen
kubectl apply -f k8s/argocd-application.yaml

# Status prüfen
kubectl get application -n argocd trade-republic-tracker

# Sync erzwingen (falls automated sync deaktiviert)
argocd app sync trade-republic-tracker
```

### 8. Alternative: Manuelles Deployment

Falls du ArgoCD nicht nutzen möchtest:

```bash
# Namespace erstellen
kubectl apply -f k8s/namespace.yaml

# Secrets & ConfigMaps
kubectl apply -f k8s/sealed-secret.yaml
kubectl apply -f k8s/nginx-config.yaml

# Storage
kubectl apply -f k8s/pvc.yaml

# Application
kubectl apply -f k8s/deployment.yaml
kubectl apply -f k8s/service.yaml
kubectl apply -f k8s/ingress.yaml
```

## Überprüfung

```bash
# Pods prüfen
kubectl get pods -n trade-republic-tracker

# Logs ansehen
kubectl logs -n trade-republic-tracker -l app=trade-republic-tracker -c php-fpm --tail=100

# Service prüfen
kubectl get svc -n trade-republic-tracker

# Ingress prüfen
kubectl get ingress -n trade-republic-tracker

# ArgoCD Status
kubectl get application -n argocd trade-republic-tracker
```

## Datenbank Migrations

Migrations werden automatisch im Init-Container ausgeführt. Falls manuell benötigt:

```bash
kubectl exec -n trade-republic-tracker deployment/trade-republic-tracker -c php-fpm -- php artisan migrate --force
```

## Troubleshooting

### Pod startet nicht

```bash
kubectl describe pod -n trade-republic-tracker -l app=trade-republic-tracker
kubectl logs -n trade-republic-tracker -l app=trade-republic-tracker -c php-fpm
```

### Datenbank Connection Fehler

Prüfe ob die PostgreSQL Datenbank erreichbar ist:

```bash
kubectl run -it --rm debug --image=postgres:15-alpine --restart=Never -- \
  psql -h postgres.postgres.svc.cluster.local -U postgres -d laravel
```

### Ingress funktioniert nicht

```bash
# Traefik Logs
kubectl logs -n kube-system -l app.kubernetes.io/name=traefik

# Ingress Status
kubectl describe ingress -n trade-republic-tracker trade-republic-tracker
```

### Secrets nicht dekodiert

Prüfe ob der Sealed Secrets Controller läuft:

```bash
kubectl get pods -n kube-system -l name=sealed-secrets-controller
kubectl logs -n kube-system -l name=sealed-secrets-controller
```

## Skalierung

```bash
# Horizontal skalieren
kubectl scale deployment/trade-republic-tracker -n trade-republic-tracker --replicas=3

# Oder in deployment.yaml anpassen und committen (ArgoCD sync)
```

## Updates

1. Neues Image bauen und pushen
2. Git committen und pushen
3. ArgoCD synct automatisch (oder manuell: `argocd app sync trade-republic-tracker`)

## Backup

Storage wird über PVC gemounted. Erstelle regelmäßige Backups:

```bash
# PVC Backup
kubectl get pvc -n trade-republic-tracker
```

Datenbank Backup über PostgreSQL Tools.

## Monitoring

Empfohlene Tools:

-   Prometheus + Grafana für Metriken
-   Loki für Logs
-   ArgoCD UI für Deployment Status

## Support

Bei Problemen:

1. Logs prüfen: `kubectl logs -n trade-republic-tracker -l app=trade-republic-tracker`
2. Events prüfen: `kubectl get events -n trade-republic-tracker`
3. ArgoCD UI: `https://argocd.deine-domain.de`
