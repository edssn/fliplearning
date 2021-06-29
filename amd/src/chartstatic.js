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
            template: `<div v-bind:id="container" @mouseenter="enterChartContainer()"></div>`,
            props: ['container', 'chart', 'lang'],
            data() {
                return { }
            },
            mounted() {
                let self = this;
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
                    },
                    plotOptions: {
                        series: {
                            events: {
                                legendItemClick: function () {
                                    self.$root.saveInteraction(this.chart.renderTo.id, "filtered", "chart_data", 8);
                                }
                            }
                        }
                    },
                });
                this._highchart = Highcharts.chart(this.container, this.chart);
            },
            methods: {
                enterChartContainer () {
                    // console.log(this);
                    // console.log(this.$root.$options.el);
                    // console.log(this.$root.$options.el.substring(1));
                    // console.log(this.$root);
                    // console.log('enterChartContainer', this.container);
                    // console.log(this._highchart);
                    // console.log(this._highchart.pointCount);
                    if (this._highchart.pointCount) {
                        this.$root.saveInteraction (this.container, "viewed", "chart_tooltip", 5);
                    }
                },
            }
        };
    });