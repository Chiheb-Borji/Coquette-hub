<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| COQUETTE HUB - STATISTICS NATIVE V1
|--------------------------------------------------------------------------
*/

class Coquette_statistics_model extends App_Model
{
    private function normalizeDays($days)
    {
        $allowed = [1, 7, 30, 90, 365];

        $days = (int) $days;

        return in_array($days, $allowed, true)
            ? $days
            : 30;
    }


    private function startInterval($days)
    {
        $days = $this->normalizeDays($days);

        return max(0, $days - 1);
    }


    public function salesSummary($days)
    {
        $interval = $this->startInterval($days);

        return $this->db->query("
            SELECT
                COALESCE(SUM(orders_count), 0) AS orders_count,
                COALESCE(SUM(revenue), 0) AS revenue
            FROM dashboard_sales_daily
            WHERE sales_date >= DATE_SUB(
                CURDATE(),
                INTERVAL {$interval} DAY
            )
        ")->row_array();
    }


    public function salesDaily($days)
    {
        $interval = $this->startInterval($days);

        return $this->db->query("
            SELECT
                sales_date,
                orders_count,
                revenue,
                updated_at
            FROM dashboard_sales_daily
            WHERE sales_date >= DATE_SUB(
                CURDATE(),
                INTERVAL {$interval} DAY
            )
            ORDER BY sales_date ASC
        ")->result_array();
    }


    public function gaSummary($days)
    {
        $interval = $this->startInterval($days);

        return $this->db->query("
            SELECT
                COALESCE(SUM(sessions), 0) AS sessions,
                COALESCE(SUM(page_views), 0) AS page_views,
                COALESCE(SUM(engaged_sessions), 0)
                    AS engaged_sessions,
                CASE
                    WHEN COALESCE(SUM(sessions), 0) > 0
                    THEN
                        COALESCE(SUM(engaged_sessions), 0)
                        / SUM(sessions)
                    ELSE 0
                END AS engagement_rate
            FROM dashboard_ga_daily
            WHERE stat_date >= DATE_SUB(
                CURDATE(),
                INTERVAL {$interval} DAY
            )
        ")->row_array();
    }


    public function gaDaily($days)
    {
        $interval = $this->startInterval($days);

        return $this->db->query("
            SELECT
                stat_date,
                active_users,
                sessions,
                page_views,
                engaged_sessions,
                engagement_rate
            FROM dashboard_ga_daily
            WHERE stat_date >= DATE_SUB(
                CURDATE(),
                INTERVAL {$interval} DAY
            )
            ORDER BY stat_date ASC
        ")->result_array();
    }



    /*
    |--------------------------------------------------------------------------
    | COQUETTE_TRAFFIC_ANALYTICS_V1_MODEL
    |--------------------------------------------------------------------------
    */

    public function trafficVsOrders($days)
    {
        $interval = $this->startInterval($days);

        return $this->db->query("
            SELECT
                g.stat_date,
                COALESCE(g.sessions, 0) AS sessions,
                COALESCE(g.page_views, 0) AS page_views,
                COALESCE(g.active_users, 0) AS active_users,
                COALESCE(g.engaged_sessions, 0) AS engaged_sessions,
                COALESCE(s.orders_count, 0) AS orders_count,
                COALESCE(s.revenue, 0) AS revenue
            FROM dashboard_ga_daily g

            LEFT JOIN dashboard_sales_daily s
                ON s.sales_date = g.stat_date

            WHERE g.stat_date >= DATE_SUB(
                CURDATE(),
                INTERVAL {$interval} DAY
            )

            ORDER BY g.stat_date ASC
        ")->result_array();
    }


    public function productSummary()
    {
        return $this->db->query("
            SELECT
                COUNT(*) AS total_products,

                COALESCE(
                    SUM(
                        CASE
                            WHEN active = 1
                            THEN 1
                            ELSE 0
                        END
                    ),
                    0
                ) AS active_products,

                COALESCE(
                    SUM(
                        CASE
                            WHEN active = 1
                                 AND quantity <= 0
                            THEN 1
                            ELSE 0
                        END
                    ),
                    0
                ) AS out_of_stock,

                COALESCE(
                    SUM(
                        CASE
                            WHEN active = 1
                                 AND quantity > 0
                            THEN 1
                            ELSE 0
                        END
                    ),
                    0
                ) AS available_products,

                COALESCE(
                    SUM(
                        CASE
                            WHEN active = 1
                            THEN price * GREATEST(quantity, 0)
                            ELSE 0
                        END
                    ),
                    0
                ) AS stock_value

            FROM dashboard_product_stats
        ")->row_array();
    }


    public function topProducts($days, $limit = 10)
    {
        $days = $this->normalizeDays($days);

        if ($days === 1) {
            $label = 'today';
        } else {
            $label = $days . ' days';
        }

        $limit = max(
            1,
            min(50, (int) $limit)
        );

        /*
        |--------------------------------------------------------------------------
        | SQL brut volontaire
        |
        | Les tables dashboard_* ne portent PAS le prefixe Perfex "tbl".
        |--------------------------------------------------------------------------
        */

        return $this->db->query(
            "
            SELECT
                id,
                period_label,
                product_id,
                product_name,
                quantity_sold,
                revenue,
                updated_at
            FROM dashboard_top_products
            WHERE period_label = ?
            ORDER BY quantity_sold DESC
            LIMIT {$limit}
            ",
            [$label]
        )->result_array();
    }


    public function products(array $filters, $limit = 50)
    {
        $limit = max(
            10,
            min(100, (int) $limit)
        );

        $q = trim(
            (string) ($filters['q'] ?? '')
        );

        $stock = (string) (
            $filters['stock'] ?? 'all'
        );

        $active = (string) (
            $filters['active'] ?? 'all'
        );

        $brand = trim(
            (string) ($filters['brand'] ?? '')
        );

        $category = trim(
            (string) ($filters['category'] ?? '')
        );


        $where = ['1=1'];
        $params = [];


        if ($q !== '') {

            $like = '%' . $q . '%';

            $where[] = "
                (
                    product_name LIKE ?
                    OR reference LIKE ?
                    OR manufacturer_name LIKE ?
                    OR default_category LIKE ?
                )
            ";

            array_push(
                $params,
                $like,
                $like,
                $like,
                $like
            );
        }


        if ($stock === 'in') {
            $where[] = 'quantity > 0';
        } elseif ($stock === 'out') {
            $where[] = 'quantity <= 0';
        }


        if ($active === '1' || $active === '0') {
            $where[] = 'active = ?';
            $params[] = (int) $active;
        }


        if ($brand !== '') {
            $where[] = 'manufacturer_name = ?';
            $params[] = $brand;
        }


        if ($category !== '') {
            $where[] = 'default_category = ?';
            $params[] = $category;
        }


        $whereSql = implode(
            ' AND ',
            $where
        );


        return $this->db->query(
            "
            SELECT *
            FROM dashboard_product_stats
            WHERE {$whereSql}
            ORDER BY
                date_upd DESC,
                product_id DESC
            LIMIT {$limit}
            ",
            $params
        )->result_array();
    }


    public function productCount(array $filters)
    {
        $q = trim(
            (string) ($filters['q'] ?? '')
        );

        $stock = (string) (
            $filters['stock'] ?? 'all'
        );

        $active = (string) (
            $filters['active'] ?? 'all'
        );

        $brand = trim(
            (string) ($filters['brand'] ?? '')
        );

        $category = trim(
            (string) ($filters['category'] ?? '')
        );


        $where = ['1=1'];
        $params = [];


        if ($q !== '') {

            $like = '%' . $q . '%';

            $where[] = "
                (
                    product_name LIKE ?
                    OR reference LIKE ?
                    OR manufacturer_name LIKE ?
                    OR default_category LIKE ?
                )
            ";

            array_push(
                $params,
                $like,
                $like,
                $like,
                $like
            );
        }


        if ($stock === 'in') {
            $where[] = 'quantity > 0';
        } elseif ($stock === 'out') {
            $where[] = 'quantity <= 0';
        }


        if ($active === '1' || $active === '0') {
            $where[] = 'active = ?';
            $params[] = (int) $active;
        }


        if ($brand !== '') {
            $where[] = 'manufacturer_name = ?';
            $params[] = $brand;
        }


        if ($category !== '') {
            $where[] = 'default_category = ?';
            $params[] = $category;
        }


        $whereSql = implode(
            ' AND ',
            $where
        );


        $row = $this->db->query(
            "
            SELECT COUNT(*) AS total
            FROM dashboard_product_stats
            WHERE {$whereSql}
            ",
            $params
        )->row_array();


        return (int) (
            $row['total'] ?? 0
        );
    }


    public function brands()
    {
        return $this->db->query("
            SELECT DISTINCT manufacturer_name
            FROM dashboard_product_stats
            WHERE manufacturer_name IS NOT NULL
              AND manufacturer_name <> ''
            ORDER BY manufacturer_name
        ")->result_array();
    }


    public function categories()
    {
        return $this->db->query("
            SELECT DISTINCT default_category
            FROM dashboard_product_stats
            WHERE default_category IS NOT NULL
              AND default_category <> ''
            ORDER BY default_category
        ")->result_array();
    }


    public function recentChanges($limit = 15, $q = '')
    {
        $limit = max(
            1,
            min(100, (int) $limit)
        );

        $q = trim(
            (string) $q
        );


        $where = ['1=1'];
        $params = [];


        if ($q !== '') {

            $like = '%' . $q . '%';

            $where[] = "
                (
                    product_name LIKE ?
                    OR reference LIKE ?
                    OR employee_name LIKE ?
                    OR message LIKE ?
                )
            ";

            array_push(
                $params,
                $like,
                $like,
                $like,
                $like
            );
        }


        $whereSql = implode(
            ' AND ',
            $where
        );


        return $this->db->query(
            "
            SELECT *
            FROM dashboard_product_changes
            WHERE {$whereSql}
            ORDER BY
                change_date DESC,
                id DESC
            LIMIT {$limit}
            ",
            $params
        )->result_array();
    }


    public function outOfStock($limit = 50, $q = '')
    {
        $limit = max(
            10,
            min(100, (int) $limit)
        );

        $q = trim(
            (string) $q
        );


        $where = [
            'active = 1',
            'quantity <= 0',
        ];

        $params = [];


        if ($q !== '') {

            $like = '%' . $q . '%';

            $where[] = "
                (
                    product_name LIKE ?
                    OR reference LIKE ?
                    OR manufacturer_name LIKE ?
                    OR default_category LIKE ?
                )
            ";

            array_push(
                $params,
                $like,
                $like,
                $like,
                $like
            );
        }


        $whereSql = implode(
            ' AND ',
            $where
        );


        return $this->db->query(
            "
            SELECT *
            FROM dashboard_product_stats
            WHERE {$whereSql}
            ORDER BY
                date_upd DESC,
                product_id DESC
            LIMIT {$limit}
            ",
            $params
        )->result_array();
    }



    /*
    |--------------------------------------------------------------------------
    | COQUETTE_PRODUCTS_AUDIT_V2_MODEL
    |--------------------------------------------------------------------------
    */


    public function productAuditProduct($productId)
    {
        $productId = (int) $productId;

        if ($productId <= 0) {
            return null;
        }

        return $this->db->query(
            "
            SELECT
                product_id,
                reference,
                product_name,
                manufacturer_name,
                default_category,
                active,
                quantity,
                price,
                date_add,
                date_upd,
                created_by,
                modified_by,
                created_log_date,
                modified_log_date
            FROM dashboard_product_stats
            WHERE product_id = ?
            LIMIT 1
            ",
            [$productId]
        )->row_array();
    }


    public function productAuditCount($productId)
    {
        $productId = (int) $productId;

        if ($productId <= 0) {
            return 0;
        }

        $row = $this->db->query(
            "
            SELECT COUNT(*) AS total
            FROM dashboard_product_audit
            WHERE id_product = ?
            ",
            [$productId]
        )->row_array();

        return (int) ($row['total'] ?? 0);
    }


    public function productAuditRows(
        $productId,
        $limit = 100,
        $offset = 0
    ) {
        $productId = (int) $productId;

        $limit = max(
            1,
            min(250, (int) $limit)
        );

        $offset = max(
            0,
            (int) $offset
        );

        if ($productId <= 0) {
            return [];
        }

        return $this->db->query(
            "
            SELECT
                id_audit,
                entity_type,
                event_type,
                id_product,
                id_product_attribute,
                product_name,
                reference,
                field_changed,
                field_label,
                old_value,
                new_value,
                change_nature,
                id_employee,
                employee_name,
                employee_email,
                ip_address,
                request_uri,
                date_add
            FROM dashboard_product_audit
            WHERE id_product = ?
            ORDER BY
                date_add DESC,
                id_audit DESC
            LIMIT {$limit}
            OFFSET {$offset}
            ",
            [$productId]
        )->result_array();
    }


    public function analytics()
    {
        return [
            'sources' => $this->db->query("
                SELECT *
                FROM dashboard_ga_sources
                WHERE period_label = '30 days'
                ORDER BY sessions DESC
                LIMIT 10
            ")->result_array(),

            'pages' => $this->db->query("
                SELECT *
                FROM dashboard_ga_pages
                WHERE period_label = '30 days'
                ORDER BY page_views DESC
                LIMIT 10
            ")->result_array(),

            'landing_pages' => $this->db->query("
                SELECT *
                FROM dashboard_ga_landing_pages
                WHERE period_label = '30 days'
                ORDER BY sessions DESC
                LIMIT 10
            ")->result_array(),

            'events' => $this->db->query("
                SELECT *
                FROM dashboard_ga_events
                WHERE period_label = '30 days'
                ORDER BY event_count DESC
                LIMIT 10
            ")->result_array(),

            'ecommerce_funnel' => $this->db->query("
                SELECT
                    event_name,
                    event_count,
                    active_users
                FROM dashboard_ga_events
                WHERE period_label = '30 days'
                  AND event_name IN (
                    'view_item',
                    'add_to_cart',
                    'view_cart',
                    'begin_checkout',
                    'add_payment_info',
                    'add_shipping_info',
                    'purchase',
                    'refund'
                  )
                ORDER BY FIELD(
                    event_name,
                    'view_item',
                    'add_to_cart',
                    'view_cart',
                    'begin_checkout',
                    'add_shipping_info',
                    'add_payment_info',
                    'purchase',
                    'refund'
                )
            ")->result_array(),

            'devices' => $this->db->query("
                SELECT *
                FROM dashboard_ga_devices
                WHERE period_label = '30 days'
                ORDER BY sessions DESC
                LIMIT 10
            ")->result_array(),

            'audience' => $this->db->query("
                SELECT *
                FROM dashboard_ga_audience
                WHERE period_label = '30 days'
                ORDER BY sessions DESC
                LIMIT 10
            ")->result_array(),

            'geo' => $this->db->query("
                SELECT *
                FROM dashboard_ga_geo
                WHERE period_label = '30 days'
                ORDER BY sessions DESC
                LIMIT 10
            ")->result_array(),

            'ecommerce' => $this->db->query("
                SELECT *
                FROM dashboard_ga_ecommerce
                ORDER BY id DESC
                LIMIT 20
            ")->result_array(),
        ];
    }


    public function syncStatus()
    {
        return [
            'sales' => $this->db
                ->query("
                    SELECT MAX(updated_at) AS value
                    FROM dashboard_sales_daily
                ")
                ->row_array(),

            'ga' => $this->db
                ->query("
                    SELECT MAX(updated_at) AS value
                    FROM dashboard_ga_daily
                ")
                ->row_array(),

            'products' => $this->db
                ->query("
                    SELECT MAX(updated_at) AS value
                    FROM dashboard_product_stats
                ")
                ->row_array(),

            'changes' => $this->db
                ->query("
                    SELECT MAX(synced_at) AS value
                    FROM dashboard_product_changes
                ")
                ->row_array(),
        ];
    }
}
