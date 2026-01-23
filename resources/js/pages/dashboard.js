import ApexCharts from 'apexcharts';

$(document).ready(function () {
    var $primary = '#7367F0';
    var $strok_color = '#b9c3cd';
    var $label_color = '#e7e7e7';
    var $primary_light = '#A9A2F6';
    var $danger_light = '#f29292';

    var revenueChartoptions = {
        chart: {
            height: 270,
            toolbar: { show: false },
            type: 'line',
        },
        stroke: {
            curve: 'smooth',
            dashArray: [0, 8],
            width: [4, 2],
        },
        grid: {
            borderColor: $label_color,
        },
        legend: {
            show: false,
        },
        colors: [$danger_light, $strok_color],

        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                inverseColors: false,
                gradientToColors: [$primary, $strok_color],
                shadeIntensity: 1,
                type: 'horizontal',
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            },
        },
        markers: {
            size: 0,
            hover: {
                size: 5
            }
        },
        xaxis: {
            labels: {
                style: {
                    colors: $strok_color,
                }
            },
            axisTicks: {
                show: false,
            },
            categories: [], // Dynamic
            axisBorder: {
                show: false,
            },
            tickPlacement: 'on',
        },
        yaxis: {
            tickAmount: 5,
            labels: {
                style: {
                    color: $strok_color,
                },
                formatter: function (val) {
                    return val > 999 ? (val / 1000).toFixed(1) + 'الف' : val;
                }
            }
        },
        tooltip: {
            x: { show: false }
        },
        series: [{
            name: "This Period",
            data: []
        },
        {
            name: "Last Period",
            data: []
        }
        ],
    };

    var revenueChart = new ApexCharts(
        document.querySelector("#revenue-chart"),
        revenueChartoptions
    );
    revenueChart.render();

    var currentPeriod = 'month';
    var currentMachine = 'sublimation';

    // Fetch and update data
    function updateChart(period, machine) {
        $.ajax({
            url: '/charts/meters',
            type: 'GET',
            data: {
                period: period,
                machine: machine
            },
            success: function (response) {
                // Update specific elements
                $('#current-revenue').text(response.currentTotal.toLocaleString());
                $('#last-revenue').text(response.lastTotal.toLocaleString());

                revenueChart.updateOptions({
                    xaxis: {
                        categories: response.labels
                    }
                });

                revenueChart.updateSeries([{
                    name: "الحالي",
                    data: response.currentData
                }, {
                    name: "السابق",
                    data: response.lastData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching chart data", xhr);
            }
        });
    }

    // Initial load
    updateChart(currentPeriod, currentMachine);

    // Event listener for Period dropdown
    $('.chart-dropdown .dropdown-item:not(.machine-item)').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        $('#dropdownItem2').text(label); // Update button text
        currentPeriod = period;
        updateChart(currentPeriod, currentMachine);
    });

    // Event listener for Machine dropdown
    $('.machine-item').on('click', function (e) {
        e.preventDefault();
        var machine = $(this).data('machine');
        var label = $(this).text();
        $('#dropdownMachine').text(label); // Update button text
        $('#chart-title').text(label); // Update card title
        currentMachine = machine;
        updateChart(currentPeriod, currentMachine);
    });

    // Orders Chart Logic
    var $warning = '#FF9F43';
    var orderChartoptions = {
        chart: {
            height: 100,
            type: 'area',
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$warning],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'الطلبات',
            data: []
        }],
        xaxis: {
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: true } // Show day name on hover
        },
    };

    var orderChart;
    if (document.querySelector("#line-area-chart-4")) {
        orderChart = new ApexCharts(
            document.querySelector("#line-area-chart-4"),
            orderChartoptions
        );
        orderChart.render();
        updateOrdersChart();
    }

    function updateOrdersChart() {
        $.ajax({
            url: '/charts/orders',
            type: 'GET',
            success: function (response) {
                $('#orders-received-total').text(response.totalOrders);
                orderChart.updateOptions({
                    xaxis: {
                        categories: response.labels
                    }
                });
                orderChart.updateSeries([{
                    name: 'الطلبات',
                    data: response.series[0].data
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching orders data", xhr);
            }
        });
    }
    // Customers Chart Logic
    var $primary = '#7367F0';
    var customersChartoptions = {
        chart: {
            height: 100,
            type: 'area',
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$primary],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'العملاء',
            data: []
        }],
        xaxis: {
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: true }
        },
    };

    var customersChart;
    if (document.querySelector("#line-area-chart-1")) {
        customersChart = new ApexCharts(
            document.querySelector("#line-area-chart-1"),
            customersChartoptions
        );
        customersChart.render();
        updateCustomersChart();
    }

    function updateCustomersChart() {
        $.ajax({
            url: '/charts/customers',
            type: 'GET',
            success: function (response) {
                $('#customers-gained-total').text(response.totalCustomers);
                customersChart.updateOptions({
                    xaxis: {
                        categories: response.labels
                    }
                });
                customersChart.updateSeries([{
                    name: 'العملاء',
                    data: response.series[0].data
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching customers data", xhr);
            }
        });
    }
});
