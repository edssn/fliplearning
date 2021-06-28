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
                                    self.$root.saveInteraction(this.renderTo.id, "downloaded", "png_chart", 6);
                                }
                            },{
                                text: this.lang.downloadJPEG,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'image/jpeg'
                                    });
                                    self.$root.saveInteraction(this.renderTo.id, "downloaded", "jpeg_chart", 6);
                                }
                            },{
                                text: this.lang.downloadPDF,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'application/pdf'
                                    });
                                    self.$root.saveInteraction(this.renderTo.id, "downloaded", "pdf_chart", 6);
                                }
                            },{
                                text: this.lang.downloadSVG,
                                onclick: function () {
                                    this.exportChart({
                                        type: 'image/svg+xml'
                                    });
                                    self.$root.saveInteraction(this.renderTo.id, "downloaded", "svg_chart", 6);
                                }
                            },{
                                text: this.lang.downloadXLS,
                                onclick: function () {
                                    this.downloadXLS();
                                    self.$root.saveInteraction(this.renderTo.id, "downloaded", "xls_chart", 6);
                                }
                            },{
                                text: this.lang.downloadCSV,
                                onclick: function () {
                                    this.downloadCSV();
                                    self.$root.saveInteraction(this.renderTo.id, "downloaded", "csv_chart", 6);
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