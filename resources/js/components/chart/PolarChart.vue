<template>
    <div class="container container-fluid">
        <div class="col-12">
            <polar-chart :chart-data="chartData"
                         :options="options"
                         :width="resultWidth" :height="resultHeight"
            >
            </polar-chart>
        </div>
    </div>
</template>

<script>
    import PolarChart from '../../charts/PolarChart'

    export default {
        components: {
            PolarChart
        },
        props: ['rawData', 'title', 'width', 'height'],
        data() {
            return {
                defaultWidth: 800,
                defaultHeight: 450,
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
                        borderColor: '#fff',
                        hoverBorderColor: '#fff',
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
                        position: 'right',
                    },
                };
            },
            resultWidth() {
                return this.width ? this.width : this.defaultWidth;
            },
            resultHeight() {
                return this.height ? this.height : this.defaultHeight;
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
