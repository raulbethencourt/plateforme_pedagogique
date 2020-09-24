import Chart from "chart.js";
import $ from "jquery";

let ctx = $('#myChart');
let myChart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['facile', 'moyen', 'dificil'],
        datasets: [{
            label: 'Dificultis',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [0, 10, 5]
        }]
    },

    // Configuration options go here
    options: {}
});