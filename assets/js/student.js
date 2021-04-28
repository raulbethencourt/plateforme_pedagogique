import Chart from "chart.js";
import $ from "jquery";

if ($('#studentChart').length) {
    let ctx = $('#studentChart');

    let studentChart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'bar',

        // The data for our dataset
        data: {
            labels: ['Compréhension écrite', 'Compréhension orale', 'Expression écrite', 'Lexique','Phonétique','Grammaire'],
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgb(141,255,99, 0.2)',
                    'rgb(141,255,99, 0.2)',
                    'rgb(141,255,99, 0.2)',
                    'rgb(141,255,99, 0.2)',
                    'rgb(60,167,221, 0.2)',
                    'rgb(203,91,91, 0.2)',
                ],
                borderColor: [
                    'rgb(141,255,99, 0.9)',
                    'rgb(141,255,99, 0.9)',
                    'rgb(141,255,99, 0.9)',
                    'rgb(141,255,99, 0.9)',
                    'rgb(60,167,221, 0.9)',
                    'rgb(203,91,91, 0.9)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem) {
                        return tooltipItem.yLabel;
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    })
}
