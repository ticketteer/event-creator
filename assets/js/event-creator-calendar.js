(function($) {

  window.tt = window.ttFunc || {};
  window.tt.checkDates = function(dateIds){
    return $.ajax({
      url: ttOpts.api_endpoint + '/public_ticket_orders/check_many',
      type: 'post',
      data: {
        lineup_date_ids: dateIds,
      },
      headers: {
        'X-Public-Key': ttOpts.public_key,
      },
      dataType: 'json'
    });
  };
  window.tt.checkDate = function(dateId) {
    return tt.checkDates([dateId]);
  };

  function getDateIds() {
    var dateIds = [];
    $('.ticketteer-book-btn').each(function(){
      var dateId = $(this).data('ticketteer-date-id');
      if (dateId) dateIds.push(dateId);
    });
    return dateIds;
  }

  function ajaxCheckDates(dateIds) {
    return ttFunc.checkDates(dateIds);
  }

  $(document).ready( function(){
    var rows = $('.event-creator-day-row');
    if ($(rows).length < 1) return;
    var dateIds = getDateIds();

    ajaxCheckDates(dateIds)
      .then(function(response){
        for( dateId in response ) {
          if (response[dateId].available < 1) {
            $('.date-'+dateId).hide();
            $('.date-sold-out-'+dateId).show();
          } else if (response[dateId].available < response[dateId].rest) {
            $('.date-'+dateId).hide();
            $('.date-rest-seats-'+dateId).show();
          }
        }
        console.log(response);
      })
      .fail(function(error){
        console.log(error);
        if (error.status === 500) {
          console.log('internal server error at ticketteer ', error.statusText);
        } else if (error.status === 404) {
          console.log('url not found: ', ttOpts.api_endpoint);
        }
      });
  });

})(jQuery);
