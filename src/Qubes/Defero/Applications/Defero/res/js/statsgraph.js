/**
 * Created by tom.kay on 04/12/13.
 */

jQuery(function() {
  var formatNumber = function (num, prec, groupsize, groupsep, decsep) {
    var p, i;
    num = (prec === false ? parseFloat(num).toString() : num.toFixed(prec)).split('');
    p = (p = $.inArray('.', num)) < 0 ? num.length : p;
    if (p < num.length) {
      num[p] = decsep;
    }
    for (i = p - groupsize; i > 0; i -= groupsize) {
      num.splice(i, 0, groupsep);
    }
    return num.join('');
  };

  var hourly = function(sparkline, options, fields) {
    var x = fields.x;
    var date = new Date();
    var hour = date.getHours()-Math.abs(23-x);
    date.setHours(hour,0,0);
    var value = formatNumber(fields.y, false, options.get('numberDigitGroupCount'), options.get('numberDigitGroupSep'), options.get('numberDecimalMark'));
    return date.toLocaleString('en-GB', {hour12:false}) +
      '<br/><span style="color: '+options.get('lineColor')+'">&#9679;</span> '+options.get('tooltipPrefix')+value+options.get('tooltipSuffix');
  };
  var daily = function(sparkline, options, fields) {
    var x = fields.x;
    var date = new Date();
    var days = date.getDate()-Math.abs(29-x);
    date.setDate(days);
    date.setHours(0,0,0);
    var value = formatNumber(fields.y, false, options.get('numberDigitGroupCount'), options.get('numberDigitGroupSep'), options.get('numberDecimalMark'));
    return date.toLocaleDateString('en-GB') +
      '<br/><span style="color: '+options.get('lineColor')+'">&#9679;</span> '+options.get('tooltipPrefix')+value+options.get('tooltipSuffix');
  };

  var spark24h = jQuery('#spark24h'),
    maxQueuedVal = parseInt(spark24h.data('max-queued')),
    maxTestVal = parseInt(spark24h.data('max-test')),
    maxSentVal = parseInt(spark24h.data('max-sent')),
    maxFailedVal = parseInt(spark24h.data('max-failed'));
  spark24h
    .sparkline('html',{tooltipFormatter:hourly,tagValuesAttribute:'data-queued', width:'350px', height:'65px',lineColor: 'blue', fillColor: false, chartRangeMax:maxQueuedVal,tooltipPrefix: 'Queued: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-sent', lineColor: 'green',fillColor: false,chartRangeMax:maxSentVal*1.3,tooltipPrefix: 'Sent: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-test', lineColor: 'orange',fillColor: false,chartRangeMax:maxTestVal*2,tooltipPrefix: 'Test: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-failed', lineColor: 'red',fillColor: false,chartRangeMax:maxFailedVal*4,tooltipPrefix: 'Failed: '});

  var spark30d = jQuery('#spark30d');
  maxQueuedVal = parseInt(spark30d.data('max-queued'));
  maxTestVal = parseInt(spark30d.data('max-test'));
  maxSentVal = parseInt(spark30d.data('max-sent'));
  maxFailedVal = parseInt(spark30d.data('max-failed'));
  spark30d
    .sparkline('html',{tooltipFormatter:daily,tagValuesAttribute:'data-queued', width:'350px', height:'65px',lineColor: 'blue',fillColor: false,chartRangeMax:maxQueuedVal,tooltipPrefix: 'Queued: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-sent', lineColor: 'green',fillColor: false,chartRangeMax:maxSentVal*1.3,tooltipPrefix: 'Sent: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-test', lineColor: 'orange',fillColor: false,chartRangeMax:maxTestVal*2,tooltipPrefix: 'Test: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-failed', lineColor: 'red',fillColor: false,chartRangeMax:maxFailedVal*4,tooltipPrefix: 'Failed: '});
});
