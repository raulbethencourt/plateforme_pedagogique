import Chart from "chart.js";
import $ from "jquery";

if ($('#teacherStudentChart').length) {
    let ctx = $('#teacherStudentChart');

    let teacherStudentChart = new Chart(ctx, {
        type: 'line',

        data: {
            labels: ['Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre'],
            datasets: [{
                data: [0, 10, 11, 10, 12, 5, 6, 2],
                label: 'Etudiants',
                borderColor: '#a051d9'
            }, {
                data: [2, 1, 0, 0, 5, 3, 0, 2],
                label: 'Formateurs',
                borderColor: '#86b067'
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

if ($('#classroomsChart').length) {
    let ctx = $('#classroomsChart');

    let classroomsChart = new Chart(ctx, {
        type: 'radar',

        data: {
            labels: ['Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre'],
            datasets: [{
                data: [0, 10, 11, 10, 12, 5, 6, 2, 8],
                borderColor: '#b33f3f'
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
        }
    })
}