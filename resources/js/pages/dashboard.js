import ApexCharts from 'apexcharts';

$(document).ready(function () {
    var $primary = '#7367F0';
    var $strok_color = '#b9c3cd';
    var $label_color = '#e7e7e7';
    var $primary_light = '#A9A2F6';
    var $danger_light = '#f29292';
    var $danger = '#EA5455';
    var $warning = '#FF9F43';
    var $warning_light = '#FFC085';


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

    var currentPeriod = 'week';
    var currentMachine = 'sublimation';

    // Fetch and update data
    function updateChart(period, machine) {
        $.ajax({
            url: '/charts/meters',
            type: 'GET',
            cache: false,
            data: {
                period: period,
                machine: machine
            },
            success: function (response) {
                if (!response) return;
                // Update specific elements
                $('#current-revenue').text((response.currentTotal || 0).toLocaleString());
                $('#last-revenue').text((response.lastTotal || 0).toLocaleString());

                if (response.labels) {
                    revenueChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                }

                revenueChart.updateSeries([{
                    name: "الحالي",
                    data: response.currentData || []
                }, {
                    name: "السابق",
                    data: response.lastData || []
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching chart data", xhr);
            }
        });
    }

    // Initial load
    updateChart(currentPeriod, currentMachine);


    function updateInventoryStock() {
        $.ajax({
            url: '/charts/inventory-stock',
            type: 'GET',
            cache: false,
            success: function (response) {
                // Update Chart and Series in one go for smoother animation
                inkChart.updateOptions({
                    colors: response.colors,
                    xaxis: {
                        categories: response.labels
                    },
                    series: response.ink_series
                });

                // Update Paper Stock
                $('#paper-stock-sub').text(response.paper.sublimation);
                $('#paper-stock-dtf').text(response.paper.dtf);

                if (response.paper.dtf > 100) {
                    $('.dtf_bar').addClass('progress-bar-primary');
                } else {
                    $('.dtf_bar').addClass('progress-bar-danger');
                }
                if (response.paper.sublimation > 100) {
                    $('.sub_bar').addClass('progress-bar-primary');
                } else {
                    $('.sub_bar').addClass('progress-bar-danger');
                }

            },
            error: function (xhr) {
                console.error("Error fetching inventory stock data", xhr);
            }
        });
    }

    // Initial Load
    updateInventoryStock();

    // Refresh every 30 seconds (sharing interval with others if possible, or independent)
    setInterval(updateInventoryStock, 30000);


    // Event listener for Period dropdown
    $('.revenue-period-item').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        $('#dropdownItem2').text(label); // Update button text
        currentPeriod = period;
        updateChart(currentPeriod, currentMachine);
    });

    // Event listener for Machine dropdown
    $('.revenue-machine-item').on('click', function (e) {
        e.preventDefault();
        var machine = $(this).data('machine');
        var label = $(this).text();
        $('#dropdownMachine').text(label); // Update button text
        $('#chart-title').text(label); // Update card title
        currentMachine = machine;
        updateChart(currentPeriod, currentMachine);
    });

    // Orders Chart Logic
    // Orders Chart Logic
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
            cache: false,
            success: function (response) {
                if (!response) return;
                $('#orders-received-total').text(response.totalOrders || 0);

                if (response.labels) {
                    orderChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                }

                var seriesData = (response.series && response.series[0]) ? response.series[0].data : [];
                orderChart.updateSeries([{
                    name: 'الطلبات',
                    data: seriesData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching orders data", xhr);
            }
        });
    }

    // Stras Orders Chart Logic
    var strasOrderChartoptions = {
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
        colors: ['#28C76F'], // Green for Stras
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
            name: 'اوردرات استراس',
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

    var strasOrderChart;
    if (document.querySelector("#line-area-chart-2")) {
        strasOrderChart = new ApexCharts(
            document.querySelector("#line-area-chart-2"),
            strasOrderChartoptions
        );
        strasOrderChart.render();
        updateStrasOrdersChart();
    }

    function updateStrasOrdersChart() {
        $.ajax({
            url: '/charts/stras-orders',
            type: 'GET',
            cache: false,
            success: function (response) {
                if (!response) return;
                $('#stras-orders-total').text(response.totalOrders || 0);

                if (response.labels) {
                    strasOrderChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                }

                var seriesData = (response.series && response.series[0]) ? response.series[0].data : [];
                strasOrderChart.updateSeries([{
                    name: 'اوردرات استراس',
                    data: seriesData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching stras orders data", xhr);
            }
        });
    }

    // Tarter Orders Chart Logic
    var tarterOrderChartoptions = {
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
        colors: [$danger], // Red for Tarter
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
            name: 'اوردرات ترتر',
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

    var tarterOrderChart;
    if (document.querySelector("#line-area-chart-3")) {
        tarterOrderChart = new ApexCharts(
            document.querySelector("#line-area-chart-3"),
            tarterOrderChartoptions
        );
        tarterOrderChart.render();
        updateTarterOrdersChart();
    }

    function updateTarterOrdersChart() {
        $.ajax({
            url: '/charts/tarter-orders',
            type: 'GET',
            cache: false,
            success: function (response) {
                if (!response) return;
                $('#tarter-orders-total').text(response.totalOrders || 0);

                if (response.labels) {
                    tarterOrderChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                }

                var seriesData = (response.series && response.series[0]) ? response.series[0].data : [];
                tarterOrderChart.updateSeries([{
                    name: 'اوردرات ترتر',
                    data: seriesData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching tarter orders data", xhr);
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
            cache: false,
            success: function (response) {
                if (!response) return;
                $('#customers-gained-total').text(response.totalCustomers || 0);

                if (response.labels) {
                    customersChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                }

                var seriesData = (response.series && response.series[0]) ? response.series[0].data : [];
                customersChart.updateSeries([{
                    name: 'العملاء',
                    data: seriesData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching customers data", xhr);
            }
        });
    }
    // Client Retention Chart Logic
    var clientChartoptions = {
        chart: {
            stacked: true,
            type: 'bar',
            toolbar: { show: false },
            height: 300,
        },
        plotOptions: {
            bar: {
                columnWidth: '10%'
            }
        },
        colors: [$primary, $danger],
        series: [{
            name: 'العملاء الجدد',
            data: []
        }, {
            name: 'العملاء المستمرون',
            data: []
        }],
        grid: {
            borderColor: $label_color,
            padding: {
                left: 0,
                right: 0
            }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 0,
            fontSize: '14px',
            markers: {
                radius: 50,
                width: 10,
                height: 10,
            }
        },
        dataLabels: {
            enabled: false
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
            categories: [],
            axisBorder: {
                show: false,
            },
        },
        yaxis: {
            tickAmount: 5,
            labels: {
                style: {
                    color: $strok_color,
                }
            }
        },
        tooltip: {
            x: { show: false }
        },
    }

    var clientChart;
    if (document.querySelector("#client-retention-chart")) {
        clientChart = new ApexCharts(
            document.querySelector("#client-retention-chart"),
            clientChartoptions
        );
        clientChart.render();
        updateClientRetentionChart('week');
    }

    function updateClientRetentionChart(period) {
        $.ajax({
            url: '/charts/client-retention',
            type: 'GET',
            cache: false,
            data: {
                period: period
            },
            success: function (response) {
                if (!response) return;
                if (response.labels) {
                    clientChart.updateOptions({
                        xaxis: {
                            categories: response.labels
                        }
                    });
                }

                var series1 = (response.series && response.series[0]) ? response.series[0].data : [];
                var series2 = (response.series && response.series[1]) ? response.series[1].data : [];

                clientChart.updateSeries([{
                    name: 'العملاء الجدد',
                    data: series1
                }, {
                    name: 'العملاء المستمرون',
                    data: series2
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching client retention data", xhr);
            }
        });
    }


    // Ink Stock Chart (Replacing Avg Session Chart)
    // ----------------------------------

    var inkChartOptions = {
        chart: {
            type: 'bar',
            height: 250,
            sparkline: { enabled: true },
            toolbar: { show: false },
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "horizontal",
                shadeIntensity: 0.5,
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 100]
            },
        },
        states: {
            hover: {
                filter: 'none'
            }
        },
        colors: [],
        series: [],
        grid: {
            show: false,
            padding: {
                left: 0,
                right: 0
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '45%',
                distributed: true,
                endingShape: 'rounded'
            }
        },
        tooltip: {
            x: { show: true },
            y: {
                formatter: function (val) {
                    return val + " لتر";
                }
            }
        },
        xaxis: {
            type: 'category',
            categories: [],
            labels: { show: false }
        },
        noData: {
            text: 'جاري التحميل...',
            style: {
                color: '#b9c3cd',
                fontSize: '14px'
            }
        }
    }

    var inkChart = new ApexCharts(
        document.querySelector("#avg-session-chart"),
        inkChartOptions
    );

    inkChart.render();


    // Ink Stock Chart ends //


    // Event listener for Client Retention Period dropdown
    var currentClientRetentionPeriod = 'week';
    $('.client-retention-period').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        $('#clientRetentionDropdown').text(label); // Update button text
        currentClientRetentionPeriod = period;
        updateClientRetentionChart(period);
    });

    // Event listener for Client Retention Period dropdown
    var currentClientRetentionPeriod = 'month';
    $('.client-retention-period').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        $('#clientRetentionDropdown').text(label); // Update button text
        currentClientRetentionPeriod = period;
        updateClientRetentionChart(period);
    });

    // console.log("Dashboard Auto-Refresh Initialized");

    // Auto-refresh all charts every 5 seconds
    setInterval(function () {
        console.log("Auto-refresh triggered...");

        // Refresh Meter Chart (main chart)
        updateChart(currentPeriod, currentMachine);

        // Refresh Orders Chart
        if (orderChart) {
            updateOrdersChart();
        }

        // Refresh Customers Chart
        if (customersChart) {
            updateCustomersChart();
        }

        // Refresh Client Retention Chart
        if (clientChart) {
            updateClientRetentionChart(currentClientRetentionPeriod);
        }
    }, 30000); // 5 seconds


    // Product Order Chart
    // -----------------------------

    var productOrderChartoptions = {
        chart: {
            height: 325,
            type: 'radialBar',
        },
        colors: [$primary, $warning, $danger, '#28C76F'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: [$primary_light, $warning_light, $danger_light, '#55D88D'],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            },
        },
        stroke: {
            lineCap: 'round'
        },
        plotOptions: {
            radialBar: {
                size: 130,
                hollow: {
                    size: '20%'
                },
                track: {
                    strokeWidth: '100%',
                    margin: 15,
                },
                dataLabels: {
                    name: {
                        fontSize: '18px',
                    },
                    value: {
                        fontSize: '16px',
                        formatter: function (val) {
                            return val;
                        }
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        }
                    }
                }
            }
        },
        series: [],
        labels: [],
        noData: {
            text: 'Loading...'
        }
    }

    var productOrderChart = new ApexCharts(
        document.querySelector("#product-order-chart"),
        productOrderChartoptions
    );

    productOrderChart.render();

    function updateInventoryChart(period = '7_days') {
        $.ajax({
            url: '/charts/inventory',
            type: 'GET',
            data: { period: period },
            cache: false,
            success: function (response) {
                const seriesData = response.series.map(Number);
                productOrderChart.updateOptions({
                    labels: response.labels
                });
                productOrderChart.updateSeries(seriesData);

                // Update Stats
                if (response.stats) {
                    $('#stat-paper-sub').text(response.stats.paper_sub + ' متر');
                    $('#stat-paper-dtf').text(response.stats.paper_dtf + ' متر');
                    $('#stat-ink-sub').text(response.stats.ink_sub + ' لتر');
                    $('#stat-ink-dtf').text(response.stats.ink_dtf + ' لتر');
                }
            },
            error: function (xhr) {
                console.error("Error fetching inventory data", xhr);
            }
        });
    }

    updateInventoryChart();

    // Inventory Period Dropdown
    var currentInventoryPeriod = '7_days';
    $('.inventory-period').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        // Updated selector to find the button within the same dropdown container
        $(this).closest('.dropdown').find('#dropdownItem2').text(label);

        currentInventoryPeriod = period;
        updateInventoryChart(period);
    });

    // Auto-refresh hook
    setInterval(function () {
        if (productOrderChart) updateInventoryChart(currentInventoryPeriod);
    }, 30000);







    // --------------------------------------------------------------------------------
    // Stras/Tarter Consumption Chart Logic
    // --------------------------------------------------------------------------------

    var strasConsumptionChartOptions = {
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
                    return val + ' M';
                }
            }
        },
        tooltip: {
            x: { show: false }
        },
        series: [{
            name: "Current Consumption",
            data: []
        },
        {
            name: "Last Consumption",
            data: []
        }
        ],
    };

    var strasConsumptionChart = new ApexCharts(
        document.querySelector("#stras-consumption-chart"),
        strasConsumptionChartOptions
    );
    strasConsumptionChart.render();

    var currentStrasPeriod = 'week';
    var currentStrasMachine = 'stras';

    function updateStrasConsumptionChart(period, machine) {
        $.ajax({
            url: '/charts/stras-tarter-consumption',
            type: 'GET',
            cache: false,
            data: {
                period: period,
                machine: machine
            },
            success: function (response) {
                // Update specific elements
                $('#current-stras-consumption').text(response.currentTotal);
                $('#last-stras-consumption').text(response.lastTotal);

                strasConsumptionChart.updateOptions({
                    xaxis: {
                        categories: response.labels
                    }
                });

                strasConsumptionChart.updateSeries([{
                    name: "الحالي",
                    data: response.currentData
                }, {
                    name: "السابق",
                    data: response.lastData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching stras consumption data", xhr);
            }
        });
    }



    // Initial Load
    updateStrasConsumptionChart(currentStrasPeriod, currentStrasMachine);

    // Event Listeners for Stras Chart
    $('.stras-period-item').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        $('#dropdownStrasPeriod').text(label);
        currentStrasPeriod = period;
        updateStrasConsumptionChart(currentStrasPeriod, currentStrasMachine);
    });

    $('.stras-machine-item').on('click', function (e) {
        e.preventDefault();
        var machine = $(this).data('machine');
        var label = $(this).text();
        $('#dropdownStrasMachine').text(label);
        $('#stras-chart-title').text(' ورق' + ' ' + label);
        currentStrasMachine = machine;
        updateStrasConsumptionChart(currentStrasPeriod, currentStrasMachine);
    });

    setInterval(function () {
        if (strasConsumptionChart) updateStrasConsumptionChart(currentStrasPeriod, currentStrasMachine);
    }, 30000);

});
