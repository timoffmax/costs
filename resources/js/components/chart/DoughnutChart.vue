<template>
    <div class="container container-fluid">
        <div class="col-12">
            <doughnut-chart :chart-data="chartData"
                            :options="options"
            >
            </doughnut-chart>
        </div>
    </div>
</template>

<script>
    import DoughnutChart from '../../charts/DoughnutChart'

    export default {
        components: {
            DoughnutChart
        },
        props: ['rawData', 'title'],
        data() {
            return {

            };
        },
        computed: {
            chartData() {
                let labels = Object.keys(this.rawData);
                let values = Object.values(this.rawData);

                for (let label in labels) {
                    labels[label] = this.$options.filters.capitalize(labels[label])
                }

                return {
                    datasets: [{
                        backgroundColor: this.getRandomColors(values.length),
                        borderColor: 'rgba(200, 200, 200, 0.75)',
                        hoverBorderColor: 'rgba(200, 200, 200, 1)',
                        data: values
                    }],
                    labels: labels
                };
            },
            options() {
                let isTitleDefined = typeof this.title !== 'undefined';

                return {
                    responsive: true,
                    maintainAspectRatio: true,
                    title: {
                        display: isTitleDefined,
                        text: this.title,
                        fontSize: 15,
                    },
                    legend: {
                        position: 'bottom',
                    },
                };
            },
        },
        methods: {
            getRandomColors(quantity) {
                let colors = [];

                for (let i = 0; i < quantity; i++) {
                    colors.push(this.getRandomRgb());
                }

                return colors;
            },
            getRandomRgb() {
                let r = Math.floor(Math.random() * 255);
                let g = Math.floor(Math.random() * 255);
                let b = Math.floor(Math.random() * 255);

                return "rgb(" + r + "," + g + "," + b + ")";
            }
        },
    }
</script>
