/**
 * Created by tom.kay on 04/12/13.
 */

jQuery(function() {
    var spark24h = jQuery('#spark24h'),
      maxQueuedVal = parseInt(spark24h.data('max-queued')),
      maxSentVal = parseInt(spark24h.data('max-sent')),
      maxFailedVal = parseInt(spark24h.data('max-failed'));
    spark24h
      .sparkline('html',{tagValuesAttribute:'data-queued', width:'350px', height:'50px',lineColor: 'blue',chartRangeMax:maxQueuedVal,tooltipPrefix: 'Queued: '})
      .sparkline('html',{composite: true, tagValuesAttribute:'data-sent', lineColor: 'green',chartRangeMax:maxSentVal*1.5,tooltipPrefix: 'Sent: '})
      .sparkline('html',{composite: true, tagValuesAttribute:'data-failed', lineColor: 'red',chartRangeMax:maxFailedVal*3,tooltipPrefix: 'Failed: '});

  var spark30d = jQuery('#spark30d'),
    maxQueuedVal = parseInt(spark30d.data('max-queued')),
    maxSentVal = parseInt(spark30d.data('max-sent')),
    maxFailedVal = parseInt(spark30d.data('max-failed'));
  spark30d
    .sparkline('html',{tagValuesAttribute:'data-queued', width:'350px', height:'50px',lineColor: 'blue',chartRangeMax:maxQueuedVal,tooltipPrefix: 'Queued: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-sent', lineColor: 'green',chartRangeMax:maxSentVal*1.5,tooltipPrefix: 'Sent: '})
    .sparkline('html',{composite: true, tagValuesAttribute:'data-failed', lineColor: 'red',chartRangeMax:maxFailedVal*3,tooltipPrefix: 'Failed: '});
});
