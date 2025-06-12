// Set default font and color to match Bootstrap
Chart.defaults.global.defaultFontFamily = "Nunito";
Chart.defaults.global.defaultFontColor = "#858796";

// Format numbers for display
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + "").replace(",", "").replace(" ", "");
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = thousands_sep || ",",
        dec = dec_point || ".",
        s = "",
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return "" + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || "").length < prec) {
        s[1] = s[1] || "";
        s[1] += new Array(prec - s[1].length + 1).join("0");
    }
    return s.join(dec);
}

// Fetch and render chart data
function fetchChartData(filters = {}) {
    $.ajax({
        //for local
        url: "/transactions/chart-data",
        //for live
        // url: "/astroguideapp/transactions/chart-data",

        method: "GET",
        data: filters,
        success: function (response) {
            renderChart(response.labels, response.datasets);
        },
        error: function (xhr) {
            console.error("Error loading chart data", xhr);
        },
    });
}

// Render Chart.js area chart
function renderChart(labels = [], datasetsByStatus = {}) {
    const ctx = document.getElementById("myAreaChart").getContext("2d");

    if (!datasetsByStatus || typeof datasetsByStatus !== "object") {
        console.error("Invalid dataset structure", datasetsByStatus);
        return;
    }

    const statusColors = {
        initiated: "rgba(255, 193, 7, 1)", // Yellow
        processing: "rgba(54, 162, 235, 1)", // Blue
        completed: "rgba(40, 167, 69, 1)", // Green
        failed: "rgba(220, 53, 69, 1)", // Red
    };

    const datasets = Object.keys(datasetsByStatus).map((status) => ({
        label: status.charAt(0).toUpperCase() + status.slice(1),
        data: datasetsByStatus[status],
        lineTension: 0.3,
        backgroundColor: "rgba(0,0,0,0)",
        borderColor: statusColors[status] || "rgba(100,100,100,1)",
        pointRadius: 3,
        pointBackgroundColor: statusColors[status],
        pointBorderColor: statusColors[status],
        pointHoverRadius: 3,
        pointHoverBackgroundColor: statusColors[status],
        pointHoverBorderColor: statusColors[status],
        pointHitRadius: 10,
        pointBorderWidth: 2,
    }));

    if (window.myLineChart instanceof Chart) {
        window.myLineChart.destroy();
    }

    window.myLineChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: datasets,
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: { left: 10, right: 25, top: 25, bottom: 0 },
            },
            scales: {
                xAxes: [
                    {
                        gridLines: { display: false, drawBorder: false },
                        ticks: {
                            maxTicksLimit: 31,
                            callback: function (value) {
                                return "Day " + value;
                            },
                        },
                    },
                ],
                yAxes: [
                    {
                        ticks: {
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function (value) {
                                return value;
                                // return number_format(value);
                            },
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2],
                        },
                    },
                ],
            },
            legend: { display: true },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: "#6e707e",
                titleFontSize: 14,
                borderColor: "#dddfeb",
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: true,
                intersect: false,
                mode: "index",
                caretPadding: 10,
                callbacks: {
                    label: function (tooltipItem, chart) {
                        const datasetLabel =
                            chart.datasets[tooltipItem.datasetIndex].label ||
                            "";
                        return (
                            datasetLabel +
                            ": " +
                            number_format(tooltipItem.yLabel)
                        );
                    },
                },
            },
        },
    });
}

// Trigger filter
$("#applyFilter").on("click", function () {
    const filters = {
        transaction_status: $("#transactionStatus").val(),
        refund_status: $("#refundStatus").val(),
        month: $("#filterMonth").val(),
    };
    fetchChartData(filters);
});

// Load chart on page ready
$(document).ready(function () {
    fetchChartData(); // Load default on page load
});
