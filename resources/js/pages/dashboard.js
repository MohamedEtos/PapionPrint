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
                    return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
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

    // Fetch and update data
    function updateChart(period) {
        $.ajax({
            url: '/chart-data',
            type: 'GET',
            data: { period: period },
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
                    name: "This Period",
                    data: response.currentData
                }, {
                    name: "Last Period",
                    data: response.lastData
                }]);
            },
            error: function (xhr) {
                console.error("Error fetching chart data", xhr);
            }
        });
    }

    // Initial load
    updateChart('month');

    // Event listener for dropdown
    $('.chart-dropdown .dropdown-item').on('click', function (e) {
        e.preventDefault();
        var period = $(this).data('period');
        var label = $(this).text();
        $('#dropdownItem2').text(label); // Update button text
        updateChart(period);
    });
});
