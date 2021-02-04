define([
        'highcharts',
        'highcharts/highcharts-3d',
        'highcharts/highcharts-more',
        'highcharts/modules/heatmap',
        'highcharts/modules/exporting',
        'highcharts/modules/export-data',
        'highcharts/modules/accessibility',
        'highcharts/modules/no-data-to-display'],
    function(Highcharts) {
    return {
        template: `<div v-bind:id="container"></div>`,
        props: ['container', 'chart'],
        data() {
            return { }
        },
        mounted() {
            this._highchart = Highcharts.chart(this.container, this.chart);
        },
        watch: {
            chart: {
                deep: true,
                handler(chart) {
                    this._highchart.update(chart);
                },
            }
        }

    };
});