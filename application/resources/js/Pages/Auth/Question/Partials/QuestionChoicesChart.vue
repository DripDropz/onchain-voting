<template>
    <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
</template>

<script lang="ts" setup>
import { Bar } from 'vue-chartjs';
import { BarChart } from 'vue-chart-3';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import QuestionData = App.DataTransferObjects.QuestionData;

ChartJS.defaults.backgroundColor = 'transparent';
ChartJS.defaults.borderColor = 'transparent';
ChartJS.defaults.color = 'white';
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps<{
    question: QuestionData;
}>();

let choicesCount = props.question?.choices_tally ?? [];
choicesCount.sort((a, b) => b.count - a.count);

let labels = choicesCount.map(choice => choice.title);
let counts = choicesCount.map(choice => choice.count);

let chartData = {
    labels: labels,
    datasets: [{
        type: "bar",
        backgroundColor: [ "white", 'teal', 'teal'],
        data: counts,
        label: props.question.title + ' Vote Results',
        borderWidth: 2,
        borderRadius: 40,
        borderSkipped: false
    }]
};

let chartOptions = {
    responsive: true,
    indexAxis: 'y',
    plugins: {
        tooltip: {
            enabled: true
        },
        legend: {
            display: false
        }
    }
};

</script>
