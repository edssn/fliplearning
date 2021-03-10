define([
        'highcharts',
        'highcharts/highcharts-3d',
        'highcharts/highcharts-more',
        'highcharts/modules/heatmap',
        'highcharts/modules/exporting',
        'highcharts/modules/export-data',
        'highcharts/modules/accessibility',
        'highcharts/modules/no-data-to-display',
        ],
    function(Highcharts) {
    return {
        template: `<div v-bind:id="container"></div>`,
        props: ['container', 'chart', 'lang'],
        data() {
            return { }
        },
        mounted() {
            (this.lang) && Highcharts.setOptions({
                lang: this.lang,
                credits: { enabled: false },
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: [{
                                text: this.lang.downloadPNG,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'image/png'
                                    });
                                }
                            },{
                                text: this.lang.downloadJPEG,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'image/jpeg'
                                    });
                                }
                            },{
                                text: this.lang.downloadPDF,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'application/pdf'
                                    });
                                }
                            },{
                                text: this.lang.downloadSVG,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'image/svg+xml'
                                    });
                                }
                            },{
                                text: this.lang.downloadXLS,
                                onclick: function () {
                                    this.downloadXLS();
                                }
                            },{
                                text: this.lang.downloadCSV,
                                onclick: function () {
                                    this.downloadCSV();
                                }
                            }],
                            symbol: 'menuball',
                            symbolStroke: '#118AB2'
                        }
                    }
                }
            });
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