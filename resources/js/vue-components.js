import Vue from 'vue';
import PieChart from './components/chart/PieChart';
import DoughnutChart from './components/chart/DoughnutChart';

Vue.component('pie-chart-custom', PieChart);
Vue.component('doughnut-chart-custom', DoughnutChart);

export default {
    pieChart: PieChart,
    doughnutChart: DoughnutChart,
};
