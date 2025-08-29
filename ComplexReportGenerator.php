<?php

class ComplexReportGenerator
{
    private array $salesData;
    private array $customers;
    private array $products;

    public function __construct()
    {
        $this->salesData = [
            ['customerId' => 1, 'productId' => 1, 'categoryId' => 1, 'regionId' => 1, 'amount' => 100, 'date' => '2024-01-01'],
            ['customerId' => 1, 'productId' => 2, 'categoryId' => 2, 'regionId' => 1, 'amount' => 200, 'date' => '2024-01-02'],
            ['customerId' => 2, 'productId' => 1, 'categoryId' => 1, 'regionId' => 2, 'amount' => 150, 'date' => '2024-01-03'],
            ['customerId' => 2, 'productId' => 3, 'categoryId' => 3, 'regionId' => 2, 'amount' => 300, 'date' => '2024-01-04'],
            ['customerId' => 3, 'productId' => 2, 'categoryId' => 2, 'regionId' => 1, 'amount' => 250, 'date' => '2024-01-05'],
            ['customerId' => 3, 'productId' => 3, 'categoryId' => 3, 'regionId' => 3, 'amount' => 400, 'date' => '2024-01-06'],
        ];

        $this->customers = [
            1 => ['name' => 'João Silva', 'tier' => 'premium'],
            2 => ['name' => 'Maria Santos', 'tier' => 'standard'],
            3 => ['name' => 'Pedro Costa', 'tier' => 'premium']
        ];

        $this->products = [
            1 => ['name' => 'Produto A', 'cost' => 50],
            2 => ['name' => 'Produto B', 'cost' => 80],
            3 => ['name' => 'Produto C', 'cost' => 120]
        ];
    }

    public function generateReport(): array
    {
        $report = ['regions' => [], 'summary' => []];
        $grandTotal = 0;
        $grandProfit = 0;
        $customersSet = [];
        $categoriesSet = [];

        foreach ($this->salesData as $sale) {
            $regionId   = $sale['regionId'];
            $categoryId = $sale['categoryId'];
            $customerId = $sale['customerId'];
            $productId  = $sale['productId'];
            $amount     = $sale['amount'];

            $profit = $amount - $this->products[$productId]['cost'];

            if (!isset($report['regions'][$regionId])) {
                $report['regions'][$regionId] = [
                    'totalSales' => 0,
                    'totalProfit' => 0,
                    'categories' => []
                ];
            }
            $region =& $report['regions'][$regionId];

            if (!isset($region['categories'][$categoryId])) {
                $region['categories'][$categoryId] = [
                    'totalSales' => 0,
                    'totalProfit' => 0,
                    'customers' => []
                ];
            }
            $category =& $region['categories'][$categoryId];

            if (!isset($category['customers'][$customerId])) {
                $category['customers'][$customerId] = [
                    'customerId' => $customerId,
                    'customerName' => $this->customers[$customerId]['name'],
                    'customerTier' => $this->customers[$customerId]['tier'],
                    'totalSales' => 0,
                    'totalProfit' => 0,
                    'orderCount' => 0,
                    'products' => []
                ];
            }
            $customer =& $category['customers'][$customerId];

            if (!isset($customer['products'][$productId])) {
                $customer['products'][$productId] = [
                    'name' => $this->products[$productId]['name'],
                    'totalSales' => 0,
                    'totalProfit' => 0,
                    'orderCount' => 0
                ];
            }
            $product =& $customer['products'][$productId];

            $region['totalSales']   += $amount;
            $region['totalProfit']  += $profit;
            $category['totalSales'] += $amount;
            $category['totalProfit']+= $profit;
            $customer['totalSales'] += $amount;
            $customer['totalProfit']+= $profit;
            $customer['orderCount']++;
            $product['totalSales']  += $amount;
            $product['totalProfit'] += $profit;
            $product['orderCount']++;

            $grandTotal += $amount;
            $grandProfit += $profit;
            $customersSet[$customerId] = true;
            $categoriesSet[$categoryId] = true;
        }

        $report['summary'] = [
            'grandTotal' => $grandTotal,
            'grandProfit' => $grandProfit,
            'totalRegions' => count($report['regions']),
            'totalCustomers' => count($customersSet),
            'totalCategories' => count($categoriesSet),
            'overallProfitMargin' => $grandTotal ? ($grandProfit / $grandTotal) * 100 : 0,
            'averageSalePerRegion' => count($report['regions']) ? $grandTotal / count($report['regions']) : 0
        ];
        return $report;
    }
}
