import Vue from 'vue';
import PieChart from './components/chart/PieChart';
import DoughnutChart from './components/chart/DoughnutChart';
import PolarChart from './components/chart/PolarChart';
import HorizontalBarChart from './components/chart/HorizontalBarChart';

Vue.component('pie-chart-custom', PieChart);
Vue.component('doughnut-chart-custom', DoughnutChart);
Vue.component('polar-chart-custom', PolarChart);
Vue.component('horizontal-bar-chart-custom', HorizontalBarChart);

export default {
    pieChart: PieChart,
    doughnutChart: DoughnutChart,
    polarChart: PolarChart,
    horizontalBarChart: HorizontalBarChart,
};
