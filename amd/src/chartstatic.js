define([
        'highcharts',
        'highcharts/highcharts-3d',
        'highcharts/highcharts-more',
        'highcharts/modules/heatmap',
        // 'highcharts/modules/exporting',
        // 'highcharts/modules/export-data',
        // 'highcharts/modules/accessibility',
        'highcharts/modules/no-data-to-display'],
    function(Highcharts) {
        return {
            template: `<div v-bind:id="container"></div>`,
            props: ['container', 'chart', 'lang'],
            data() {
                return { }
            },
            mounted() {
                (this.lang) && Highcharts.setOptions({lang: this.lang, credits: { enabled: false }});
                this._highchart = Highcharts.chart(this.container, this.chart);
            }
        };
    });