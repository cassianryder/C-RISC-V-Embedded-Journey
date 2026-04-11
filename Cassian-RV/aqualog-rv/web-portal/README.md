# AquaLog-RV Web Portal

A PHP + CSS + MySQL web portal prototype for aquaculture telemetry, alerting, device control, and future AI-assisted operations.

## What This Prototype Covers

- customer-facing landing page
- operational dashboard for water-quality telemetry
- device status and control view
- alert center
- analytics and prediction overview
- MySQL schema for future production integration

## Intended End-State

This portal is designed as the web-facing layer for a broader system:

- `Milk-V Duo S / RISC-V` edge device for real-time sensor acquisition
- telemetry ingestion and persistence
- expert-rule alerting
- ML-based prediction and recommendation
- oxygen pump and alarm linkage
- underwater shrimp vision analytics
- deployment to domestic or international servers with a public domain

## Directory Layout

```text
web-portal/
├── README.md
├── app/
│   ├── bootstrap.php
│   ├── config.php
│   ├── data/
│   │   └── sample_data.php
│   └── lib/
│       ├── db.php
│       ├── helpers.php
│       └── repositories.php
├── database/
│   ├── schema.sql
│   └── seed.sql
└── public/
    ├── index.php
    ├── dashboard.php
    ├── devices.php
    ├── alerts.php
    ├── analytics.php
    ├── control.php
    ├── partials/
    │   ├── footer.php
    │   └── header.php
    └── assets/
        └── css/
            └── styles.css
```

## Runtime Model

The portal tries MySQL first. If a database connection is not available, it falls back to local sample data so the UI can still be demonstrated.

That makes it useful in two phases:

1. portfolio/demo phase
2. production integration phase

## Local Setup

1. Create a MySQL database, for example `aqualog_rv`
2. Run the schema:

```bash
mysql -u root -p aqualog_rv < database/schema.sql
mysql -u root -p aqualog_rv < database/seed.sql
```

3. Copy `app/config.php` and update database credentials if needed
4. Serve the `public/` directory from PHP

Example:

```bash
php -S 127.0.0.1:8000 -t public
```

## Production Direction

Recommended future split:

- edge collector on Milk-V Duo S
- API service for ingestion and command dispatch
- MySQL or time-series storage
- model service for forecasting and anomaly scoring
- PHP portal for operations and customer view

## Suggested Next Steps

- replace sample repositories with real MySQL queries only
- add login and role-based access
- add historical chart pages
- add REST endpoints for device control
- connect to actual telemetry uploaded by the simulator or embedded node
