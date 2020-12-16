define([
        'highcharts',
        'highcharts/highcharts-3d',
        'highcharts/highcharts-more',
        'highcharts/modules/exporting',
        'highcharts/modules/export-data',
        'highcharts/modules/accessibility'],
    function(Highcharts) {
    return {
        template: `<div id="container"></div>`,
        props: ['chart'],
        mounted() {
            this._highchart = Highcharts.chart(this.$el, this.chart);
        },
    };
});