/**
 * Created by tom.kay on 04/12/13.
 */

jQuery(function() {
  var spark24h = jQuery('#spark24h'),
    maxQueuedVal = parseInt(spark24h.data('max-queued')),
    maxTestVal = parseInt(spark24h.data('max-test')),
    maxSentVal = parseInt(spark24h.data('max-sent')),
    maxFailedVal = parseInt(spark24h.data('max-failed'));
  spark24h
    .sparkline('html',{tagValuesAttribute:'data-queued', width:'350px', height:'65px',lineColor: 'blue', fillColor: false, chartRangeMax:maxQueuedVal,tooltipPrefix: 'Queued: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-test', lineColor: 'orange',fillColor: false,chartRangeMax:maxTestVal*1.5,tooltipPrefix: 'Test: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-sent', lineColor: 'green',fillColor: false,chartRangeMax:maxSentVal*1.5,tooltipPrefix: 'Sent: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-failed', lineColor: 'red',fillColor: false,chartRangeMax:maxFailedVal*3,tooltipPrefix: 'Failed: '});

  var spark30d = jQuery('#spark30d');
  maxQueuedVal = parseInt(spark30d.data('max-queued'));
  maxTestVal = parseInt(spark30d.data('max-test'));
  maxSentVal = parseInt(spark30d.data('max-sent'));
  maxFailedVal = parseInt(spark30d.data('max-failed'));
  spark30d
    .sparkline('html',{tagValuesAttribute:'data-queued', width:'350px', height:'65px',lineColor: 'blue',fillColor: false,chartRangeMax:maxQueuedVal,tooltipPrefix: 'Queued: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-test', lineColor: 'orange',fillColor: false,chartRangeMax:maxTestVal*1.5,tooltipPrefix: 'Test: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-sent', lineColor: 'green',fillColor: false,chartRangeMax:maxSentVal*1.5,tooltipPrefix: 'Sent: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-failed', lineColor: 'red',fillColor: false,chartRangeMax:maxFailedVal*3,tooltipPrefix: 'Failed: '});
});
