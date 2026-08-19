# Coquette HUB Architecture

## Application Layer

The custom HUB extends an existing CRM application with custom
business modules.

Custom components include:

- Dashboard
- Sidebar/navigation
- Statistics
- Team ToDo
- Marketing Workspace
- Role-based permissions
- FR/EN translations

## E-commerce Integration

PrestaShop provides business and customer information.

A separate synchronization process is used to populate the CRM.

## Analytics

Google Analytics data is combined with PrestaShop information
to provide operational KPIs.

## Production Infrastructure

The production application runs in an LXC container managed
through Proxmox.

Traffic is routed using Nginx and HTTPS.

## Security

Production secrets and customer data are intentionally excluded
from this public source package.
