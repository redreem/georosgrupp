module.exports = {

    id_firm: 0,
    firm_name: '',
    net_name: '',
    id_net: 0,
    is_net: false,
    id_rubr: 0,
    id_karta: 0,
    id_okrug: 0,
    id_gorod: 0,
    calc_only_net: false,
    net_firm_ids_str: '',
    net_rubrics_ids_str: '',
    net_karta_ids_str: '',

    base_url: '/statistics/',

    data1: [],
    data1_new: [],
    data1_cat: [],
    data1_1: [],
    data1_2: [],
    data2: [],
    data3: [],

    visiters_start: 0,
    visiters_rows: 10,
    visiters_has_more: 0,

    avg_count_on_firm_by_last_month: 0,
    count_in_rubr_and_karta: 0,
    count_in_rubr_and_karta_nopayers: 0,
    count_in_rubr_and_okrug: 0,
    firm_traf_percent: 0,
    count_emulator_int: false,
    count_emulator_int_okrug: false,

    options1: {// опции графика
        width: '100%',
        height: 300,
        backgroundColor: 'none',
        bar: {
            groupWidth: '85%',
            groupHeight: '100%'
        },
        legend: {
            position: 'none'
        },
        isStacked: true,
        chartArea: {
            height: '70%',
            left: 40,
            right: 0,
            top: 50
        },
        animation: {
            startup: true,
            duration: 1000,
            easing: 'out'
        },
        colors: ["#FBB53C", "#C09ECE", "#FFEB00"]
    },

    options2: {
        title: '',
        backgroundColor: 'none',
        bar: {groupWidth: '100%'},
        chartArea: {height: 250, width: 250, left: 25, top: 10},
        isStacked: true,
        animation: {
            startup: true,
            duration: 1000,
            easing: 'out'

        },
        legend: {position: "bottom"},
        pieSliceText: 'percentage',
        tooltip: {
            'text': 'percentage'
        }
    },

    options3: {
        title: '',
        width: '100%',
        height: '100%',
        curveType: 'function',
        legend: {position: 'none'},
        backgroundColor: 'none',
        bar: {groupWidth: '100%'},
        chartArea: {
            height: '85%',
            width: '100%',
            left: 50,
            top: 10
        },
        /* выключена, т.к. будет мигать замена на русские названия месяцев
        animation:{
            startup: true,
            duration: 1000,
            easing: 'out'
        },
        */
        isStacked: true,
        colors: [
            '#059f05',
            '#df2828',
            '#ffa500',
            '#5d5d5d',
            '#5bc6e9',
            '#d100d1',
            '#006585'
        ]
    },

    statistics_preloader: '',

    error_message: FE.getData('error_message'),

    load_counters: function (cb) {
        var $this = this;
        $.ajax({
            url: this.base_url,
            data: {
                ajax: 1,
                action: 'counters',
                id_firm: this.id_firm,
                is_net: (this.is_net ? 1 : 0),
                net_firm_ids_str: this.net_firm_ids_str,
                net_rubrics_ids_str: this.net_rubrics_ids_str,
                net_karta_ids_str: this.net_karta_ids_str,
                net_okrug_ids_str: this.net_okrug_ids_str,
                calc_only_net: this.calc_only_net,
            },
            dataType: 'json',
            type: 'POST',
            success: function (data) {
                $this.avg_count_on_firm_by_last_month = data['avg_count_on_firm_by_last_month'];
                $this.count_in_rubr_and_karta = data['count_in_rubr_and_karta'];
                $this.count_in_rubr_and_karta_nopayers = data['count_in_rubr_and_karta_nopayers'];
                $this.firm_traf_percent = data['firm_traf_percent'];
                if (typeof cb === 'function') {
                    setTimeout(cb, 1500);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#count_log_firm, #count_log_rubr, #count_log_rubr_okrug')
                    .text($this.error_message)
                    .addClass('has_error');
                console.log($this.error_message);
                console.dir(jqXHR.status, textStatus, errorThrown);
            }
        });
    },

    load_counter_by_okrug: function () {
        var $this = this;
        $.ajax({
            url: this.base_url,
            data: {
                ajax: 1,
                action: 'counterbyokrug',
                id_firm: this.id_firm,
                is_net: (this.is_net ? 1 : 0),
                net_firm_ids_str: this.net_firm_ids_str,
                net_rubrics_ids_str: this.net_rubrics_ids_str,
                net_karta_ids_str: this.net_karta_ids_str,
                net_okrug_ids_str: this.net_okrug_ids_str,
                calc_only_net: this.calc_only_net,
            },
            dataType: 'json',
            type: 'POST',
            success: function (data) {
                $this.count_in_rubr_and_okrug = data['counter_by_okrug'];
                $this.count_log_emulator_okrug();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#count_log_rubr_okrug')
                    .text($this.error_message)
                    .addClass('has_error');
                console.log($this.error_message);
                console.dir(jqXHR.status, textStatus, errorThrown);
            }
        });
    },

    prepare_interface: function () {
        this.statistics_preloader = $('#statistics_preloader').html();
        var statistics_preloader = $('#statistics_preloader');

        if (statistics_preloader.length) {
            this.statistics_preloader = statistics_preloader[0].outerHTML;
        }

        if (this.id_firm) {
            $('#sel_city, #sel_region').css({display: 'inline-block'});
            $('#sel_city').addClass('activeTerritory');
        }
    },

    chartConcurentov: function () {
        google.charts.load('current', {'packages': ['corechart']});

        var $this = this;
        google.charts.setOnLoadCallback(function () {
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
            chart.draw(
                google.visualization.arrayToDataTable($this.data3),
                $this.options3
            );
            replace_month();
        });

        var month_map = FE.getData('month_map');
        month_map = JSON.parse(month_map);

        var replace_month = function () {
            var month_text_jquery_object = $('#curve_chart > div > div:nth-child(1) > div > svg > g:nth-child(2) > g:nth-child(4) g > text');
            month_text_jquery_object.each(function (index) {
                var t = $(month_text_jquery_object.get(index));
                var text_content = t.text();
                if (isNaN(text_content)) {
                    for (var i in month_map) {
                        if (month_map.hasOwnProperty(i)) {
                            if (text_content.indexOf(i) !== -1) {
                                t.html(text_content.replace(i, month_map[i]));
                            }
                        }
                    }
                }
            });
        };
    },

    count_log_emulator: function () {

        var step_1 = this.avg_count_on_firm_by_last_month / 20;
        var step_2 = this.count_in_rubr_and_karta / 20;
        var step_2_1 = this.count_in_rubr_and_karta_nopayers / 20;

        var count_1 = 0;
        var count_2 = 0;
        var count_2_1 = 0;

        var $this = this;

        this.count_emulator_int = window.setInterval(function () {

            count_1 += step_1;
            count_2 += step_2;
            count_2_1 += step_2_1;

            if (count_1 > $this.avg_count_on_firm_by_last_month) {

                $('#count_log_firm').html($this.avg_count_on_firm_by_last_month);
            } else {

                $('#count_log_firm').html(Math.floor(count_1));
            }

            if (count_2 > $this.count_in_rubr_and_karta) {

                $('#count_log_rubr').html($this.count_in_rubr_and_karta);
            } else {

                $('#count_log_rubr').html(Math.floor(count_2));
            }

            if ($this.count_in_rubr_and_karta_nopayers != $this.count_in_rubr_and_karta) {
                if (count_2_1 > $this.count_in_rubr_and_karta_nopayers) {

                    $('#count_log_rubr_nopayers').html('<span class="nopayers_count_message">' + FE.getData('nopayers_count_message') + '</span>' + $this.count_in_rubr_and_karta_nopayers + ')');
                } else {

                    $('#count_log_rubr_nopayers').html('<span class="nopayers_count_message">' + FE.getData('nopayers_count_message') + '</span>' + Math.floor(count_2_1) + ')');
                }
            }

            if (
                (count_1 > $this.avg_count_on_firm_by_last_month)
                &&
                (count_2 > $this.count_in_rubr_and_karta)
                &&
                (count_2_1 > $this.count_in_rubr_and_karta_nopayers)
            ) {
                $('#attendanceMonthPercent').text($this.firm_traf_percent);
                $('.attendanceMonthPercent').addClass('visible');
                window.clearInterval($this.count_emulator_int);
            }

        }, 80);
    },

    count_log_emulator_okrug: function () {

        var step_3 = this.count_in_rubr_and_okrug / 20;

        var count_3 = 0;

        var $this = this;

        this.count_emulator_int_okrug = window.setInterval(function () {

            count_3 += step_3;

            if (count_3 > $this.count_in_rubr_and_okrug) {

                $('#count_log_rubr_okrug').html($this.count_in_rubr_and_okrug);
            } else {

                $('#count_log_rubr_okrug').html(Math.floor(count_3));
            }

            if (
                (count_3 > $this.count_in_rubr_and_okrug)
            ) {
                window.clearInterval($this.count_emulator_int_okrug);
            }

        }, 80);
    },

    show_more_visiters: function () {

        $('#data_3').remove();
        $('#data_3_1').remove();

        var $this = this;

        this.visiters_start += this.visiters_rows;

        $.ajax({
            url: this.base_url,
            data: {
                ajax: 1,
                action: 'visiters',
                id_firm: this.id_firm,
                id_net: this.id_net,
                calc_only_net: (this.calc_only_net ? 1 : 0),
                net_firm_ids_str: this.net_firm_ids_str,
                net_rubrics_ids_str: this.net_rubrics_ids_str,
                net_karta_ids_str: this.net_karta_ids_str,
                start: this.visiters_start,
                rows: this.visiters_rows,
                request_data_type: 'json'
            },
            dataType: 'json',
            type: 'POST',
            success: function (data) {
                $('#visiters_table_body').append(data.rows);
                $this.visiters_has_more = data.has_more;
                $this.set_visiters_more_button();
            }
        });
    },

    set_visiters_more_button: function () {

        if (this.visiters_has_more !== 1) {
            $('#show_more_visiters_but').css({display: 'none'});
        } else {
            $('#show_more_visiters_but').css({display: 'inline-flex'});
        }
    },

    people_postfix_by_number: function (number) {

        number = String(number);
        var numbers = [2, 3, 4];//конечные цифры для добавления окончания А к человек
        var last_digit = number.substring(number.length - 1, number.length);
        for (var i in numbers) {
            if (numbers[i] == last_digit) {
                return 'а';
            }
        }
        return '';
    },

// круговая диаграмма
    load_data_3_4: function () {

        var $this = this;
        $.ajax({
            url: this.base_url,
            data: {
                ajax: 1,
                action: 'load_data_3_4',
                id_firm: this.id_firm,
                id_net: this.id_net,
                calc_only_net: (this.calc_only_net ? 1 : 0),
                net_firm_ids_str: this.net_firm_ids_str,
                net_rubrics_ids_str: this.net_rubrics_ids_str,
                net_karta_ids_str: this.net_karta_ids_str,
                id_rubr: this.id_rubr,
                id_karta: this.id_karta,
                id_gorod: this.id_gorod,
                request_data_type: 'json'
            },
            dataType: 'json',
            type: 'POST',
            success: function (data) {
                //console.log(data);
                $('#loaded_data_3').parent().find('.loading').remove();
                $('#loaded_data_4').parent().find('.loading').remove();

                $('#loaded_data_3').html(data.visiters);
                $this.visiters_start = 0;
                $this.visiters_rows = 10;
                $this.visiters_has_more = data.has_more;
                $this.set_visiters_more_button();

                $this.data2 = eval(data.chart_data);

                $('#loaded_data_3, #loaded_data_4').css({display: 'block'});
                // $this.chart_circle();
            },
            complete: function () {
                var $$this = $this;
                window.setTimeout(function () {
                    $$this.load_data_5_6();
                }, 300);
            }
        });
    },

    load_data_5_6: function () {

        var $this = this;
        var data = {
            ajax: 1,
            action: 'load_data_5_6',
            id_net: this.id_net,
            id_firm: this.id_firm,
            firm_name: this.firm_name,
            net_name: this.net_name,
            net_firm_ids_str: this.net_firm_ids_str,
            net_rubrics_ids_str: this.net_rubrics_ids_str,
            net_karta_ids_str: this.net_karta_ids_str,
            net_okrug_ids_str: this.net_okrug_ids_str,
            calc_only_net: this.calc_only_net,
            id_rubr: this.id_rubr,
            id_karta: this.id_karta,
            id_okrug: this.id_okrug
        };

        $.ajax({
            url: this.base_url,
            data: data,
            dataType: 'html',
            type: 'POST',
            success: function (data) {
                //console.log(data);
                $('#loaded_data').html(data);

                $('#loaded_data_6').parent().find('.loading').remove();

                $this.data3 = eval($('#data_5').html());

                for (var i = 1; i < $this.data3.length; i++) {

                    $this.data3[i][0] = new Date($this.data3[i][0]);
                }
                $this.chartConcurentov();

                $('#loaded_data_6').html($('#data_6').html());
                $('#loaded_data_6_1').html($('#data_6_1').html());
            }
        });
    },
//новый  график бар
    update_graph2_new: function () {
        var $this = this;
        var sel1_val;
        var sel2_val;
        var container2 = $('#container2');

        if (!this.calc_only_net) {
            if ($('#sel_city').hasClass('activeTerritory')) {

                sel1_val = 2;

                $('#attendanceMonthCompany').css({display: 'flex'});
                $('#attendanceMonthRubric').css({display: 'flex', order: 3});
                $('#attendanceMonthOkrug').css({display: 'none'});

            } else {
                sel1_val = 3;

                $('#attendanceMonthCompany').css({display: 'none'});
                $('#attendanceMonthRubric').css({display: 'flex', order: 1});
                $('#attendanceMonthOkrug').css({display: 'flex'});
            }
        } else {
            sel1_val = 2;
        }

        $('.select2 option').each(function () {
            if ($(this).get(0).selected) {
                sel2_val = $(this).attr('value');
                return false;
            }
        });

        container2.html($this.statistics_preloader);

        $.ajax({
            url: this.base_url,
            data: {
                ajax: 1,
                action: 'graph2_new',
                sel1: sel1_val,
                sel2: sel2_val,
                id_firm: this.id_firm,
                is_net: (this.is_net ? 1 : 0),
                net_firm_ids_str: this.net_firm_ids_str,
                net_rubrics_ids_str: this.net_rubrics_ids_str,
                net_karta_ids_str: this.net_karta_ids_str,
                calc_only_net: this.calc_only_net,
                id_net: $this.id_net,
                id_rubr: $this.id_rubr,
                id_karta: $this.id_karta
            },
            dataType: 'html',
            type: 'POST',
            success: function (data) {
                console.log(data);
                $this.data1_new = eval(data);
                //$this.graph2_new();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.log("000000000");
                container2.text($this.error_message).addClass('has_error');
                console.log($this.error_message);
                console.dir(jqXHR.status, textStatus, errorThrown);
            },
            complete: function () {

                if ($this.count_in_rubr_and_okrug == 0 && $('#sel_region').hasClass('activeTerritory')) {

                    $this.load_counter_by_okrug();
                }
            }
        });

    },
};
/////////////////////////пример массива для графика//////////////////////////////////////////
//Highcharts.chart('container2', { chart: {  type: "column" }, title: {  text: "" }, xAxis: {  categories: [   '26.10', '27.10', '28.10', '29.10', '30.10', '31.10', '01.11', '02.11', '03.11', '04.11', '05.11', '06.11', '07.11', '08.11', '09.11', '10.11', '11.11', '12.11', '13.11', '14.11', '15.11', '16.11', '17.11', '18.11', '19.11', '20.11', '21.11', '22.11', '23.11', '24.11', '25.11', '26.11', '27.11', '28.11', '29.11', '30.11', '01.12', '02.12', '03.12', '04.12', '05.12', '06.12', '07.12', '08.12', '09.12'   ] }, yAxis: [{  min: 0,  title: {  text: ""  } }], legend: {  shadow: false }, tooltip: {  shared: true }, plotOptions: {  column: {  grouping: false,  shadow: false,  borderWidth: 0  } }, series: [{  name: "Посещаемость рубрики Стоматологические клиники в районе Тропарёво-Никулино",  color: "#c09ece",  data: [  19, 16, 9, 12, 39, 29, 32, 28, 29, 23, 12, 36, 19, 25, 16, 22, 18, 18, 12, 11, 6, 8, 5, 7, 16, 15, 17, 16, 11, 3, 3, 7, 64, 9, 15, 3, 20, 15, 14, 16, 41, 8, 0, 44, 10 ],  pointPadding: 0,  pointPlacement: 0 }, {  name: "Посещаемость страницы Сальвео ",  color: "#fbb53c",  data: [  0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 2, 1, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0 ],  pointPadding: 0,  pointPlacement: 0 }] });
////////////////////////пример массива круговой диаграммы /////////////////////////////////////
/*
Highcharts.chart('piechart_new', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    subtitle: {
        text: ''
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.percentage:.1f} %',
                distance: -30,
                filter: {
                    property: 'percentage',
                    operator: '>',
                    value: 6
                }
            },
            showInLegend: true
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.percentage:.1f} %</b> всего<br/>'
    },
    "series": [
        {
            "name": "Браузеры",
            "colorByPoint": true,
            "data": [
                {
                    "name": "Chrome",
                    "y": 62,
                },
                {
                    "name": "Firefox",
                    "y": 10,
                },
                {
                    "name": "Internet Explorer",
                    "y": 7,
                },
                {
                    "name": "Safari",
                    "y": 5,
                },
                {
                    "name": "Edge",
                    "y": 4,
                    // "drilldown": "Edge"
                },
                {
                    "name": "Opera",
                    "y": 1,
                },
                {
                    "name": "Other",
                    "y": 7,
                }
            ]
        }
    ],

});
*/
////////////////////////пример массива линейной диаграммы /////////////////////////////////////
/*
Highcharts.chart('container', {
  chart: {
    type: 'spline',
    description: ''
  },

  legend: false,
  title: {
    text: ''
  },
  subtitle: {
    text: ''
  },
  yAxis: {
    title: {
      text: ''
    }
  },
  xAxis: {
    title: {
      text: ''
    },
    description: '',
    categories: ['2018-09', '2018-10', '2018-11']
  },
  tooltip: {
    split: true,
  },

  series: [

    {
      name: 'JAWS',
      data: [74, 69.6, 63.7],
       marker: {
        "enabled": false
      },

    }, {
      name: 'NVDA',
      data: [8, 34.8, 43.0],
       marker: {
        "enabled": false
      },
    }, {
      name: 'VoiceOver',
      data: [6, 20.2, 30.7],
         marker: {
        "enabled": false
      },
    }
  ]
});
*/