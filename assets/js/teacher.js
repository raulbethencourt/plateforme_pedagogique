import $ from "jquery";
import Chart from "chart.js";

if ($('#questionnairesChart').length) {
    let ctx = $('#questionnairesChart');

    let questionnairesChart = new Chart(ctx, {
        type: 'line',

        data: {
            labels: ['Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre'],
            datasets: [{
                data: [0, 5, 2, 1, 2, 3, 1, 2],
                label: 'Questionnaires',
                borderColor: '#e3d97f'
            }]
        },
        options: {
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