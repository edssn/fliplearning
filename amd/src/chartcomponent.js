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
        template: `<div id="container"></div>`,
        props: ['chart'],
        mounted() {
            this._highchart = Highcharts.chart(this.$el, this.chart);
        },
    };
});