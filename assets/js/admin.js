;( function($) {

  var activeClassName = 'event-creator-active';

  $( function() {

    $('#event-creator-tabs-nav a').on('click', showEventsTab);
    $('#event-creator-new-date').on('click', showEditDateForm);
    $('#event-creator-save-date-btn').on('click', ajaxSaveDate);
    $('.event-creator-title').on('click', '.event-creator-close-btn', hideEditDateForm);

    // dates-list actions
    $('#event-creator-dates-list-tbody').on('click', '.event-creator-delete-date-link', deleteDate);
    $('#event-creator-dates-list-tbody').on('click', '.event-creator-edit-date-link', editDate);

  }); // end of main $(function(){ - call

  /**
   * toggle the edit date form
   *
   * @param  {object} e Javascript event object
   *
   */
  function showEditDateForm(e){
    e.preventDefault();
    $('#event-creator-date-form').show();
    $('#event-creator-dates-list').hide();
    var $title = $(this).closest('.event-creator-tab').find('.title');
    $title.html(
      '<span>' + $(this).attr('data-edit-text') + '</span>' +
      '<span class="event-creator-close-btn dashicons dashicons-no-alt"></span>'
    );
    $('#event-creator-datepicker').datepicker({
      altField: '#event-creator-date-starts_at_date',
      altFormat: 'yy-mm-dd',
    });
    $('#event-creator-save-date-btn').text($('#event-creator-save-date-btn').attr('data-create-text'));
  }

  function deleteDate(e){
    e.preventDefault();
    var really = confirm('Really delete date?');
    if (!really) return;
    var $row = $(this).closest('tr');
    var date_id = $row.attr('data-date-id');
    $.getJSON( ajaxurl, {
      action: 'event_creator_delete_date',
      date_id: date_id,
    })
    .done(function(){
      $row.fadeOut();
    });
  }

  function hideEditDateForm(e){
    e.preventDefault();
    $('#event-creator-date-form').hide();
    $('#event-creator-dates-list').show();
    var $title = $(this).closest('.event-creator-tab').find('.title');
    $title.text($(this).closest('.event-creator-title').attr('data-orig-title'));
  }

  /**
   * show the according tab selected from the navigation
   *
   * @param  {object} e Javascript event object
   *
   */
  function showEventsTab(e){
    e.preventDefault();
    if ( $(this).hasClass(activeClassName) ) return;
    $('#event-creator-tabs-nav a').removeClass(activeClassName);
    $(this).addClass(activeClassName);
    var activeIndex = -1;
    $('#event-creator-tabs-nav a').each( function(index){
      if ( !$(this).hasClass(activeClassName) ) return;
      activeIndex = index;
    });
    $('#event-creator-tabs .event-creator-tab').removeClass(activeClassName);
    var activeTab = $('#event-creator-tabs .event-creator-tab')[activeIndex];
    $(activeTab).addClass(activeClassName);
    if ($(activeTab).attr('data-tab') === 'dates') {
      loadDates($(activeTab).attr('data-post-id'));
    }
  }

  /**
   * show the according tab selected from the navigation
   *
   * @param  {object} e Javascript event object
   *
   */
  function editDate(e){
    e.preventDefault();
    var tr = e.target.closest('tr');
    $('#event-creator-date-form').show();
    $('#event-creator-dates-list').hide();
    var $title = $(tr).closest('.event-creator-tab').find('.title');
    $title.html(
      '<span>' + $(tr).data('title') + '</span>' +
      '<span class="event-creator-close-btn dashicons dashicons-no-alt"></span>'
    );
    $('#event-creator-datepicker').datepicker({
      altField: '#event-creator-date-starts_at_date',
      altFormat: 'yy-mm-dd',
      dateFormat: 'yy-mm-dd',
    });
    $('#event-creator-datepicker').datepicker('setDate', $(tr).data('starts_at_date'));
    $('#event-creator-date-starts_at_date').val($(tr).data('starts_at_date'));
    $('#event-creator-date-starts_at_time').val($(tr).data('starts_at_time'));
    $('#event-creator-date-note').val($(tr).data('note'));

    if( $(tr).data('cancelled') === 1 ) {
      $('#event-creator-date-cancelled').attr('checked', true);
    } else {
      $('#event-creator-date-cancelled').attr('checked', false);
    }

    if( $(tr).data('premiere') === 1 ) {
      $('#event-creator-date-premiere').attr('checked', true);
    } else {
      $('#event-creator-date-premiere').attr('checked', false);
    }

    $('#event-creator-save-date-btn').text($('#event-creator-save-date-btn').attr('data-edit-text'));
    $('#event-creator-save-date-btn').attr('data-date-id', $(tr).data('date-id'));
  }

  /**
   * sends an ajax request and loads all dates
   *
   */
  function loadDates(event_id){
    $.getJSON( ajaxurl, {
        action: 'event_creator_list_dates',
        event_id: event_id,
      },
      function(response) {
        if (!response.data) return;
        $('#event-creator-dates-list-tbody').html('');
        for( date_key in response.data ){
          var date = response.data[date_key];
          addDateRow(date);
        }
      }
    );
  }

  /**
   * adds a date to the dates-list-tbody
   *
   * @param object date a date object
   */
  function addDateRow(date){
    var cancelledText = date.meta.cancelled === '1' ?
      '<span class="cancelled-text">' + $('#event-creator-dates-list-tbody').attr('data-i18n-cancelled') + '</span> &nbsp; ' :
      '';
    var premiereText = date.meta.premiere === '1' ?
      '<span class="premiere-text">&#9728;</span> &nbsp; ' :
      '';
    var ticketteerLinkCell = '<td>' +
        '<a target="_blank" href="' + date.meta.ticketteer_link + '">' +
        $('#event-creator-dates-list-tbody').attr('data-i18n-booking-list') +
      '</td>';
    rowHtml = '' +
      '<tr data-date-id="' + date.ID + '" ' +
        'data-starts_at_date="' + date.meta.starts_at_date + '" ' +
        'data-title="' + date.post_title + '" ' +
        'data-note="' + date.meta.note + '" ' +
        'data-cancelled="' + date.meta.cancelled + '" ' +
        'data-premiere="' + date.meta.premiere + '" ' +
        'data-starts_at_time="' + date.meta.starts_at_time + '" ' +
        'data-venue_id="' + date.meta.venue_id + '" ' +
        'class=' + (date.meta.cancelled === '1' ? 'cancelled' : '') +
      '>' +
        '<td>' + date.meta.starts_at_weekday + '</td>' +
        '<td>' + date.meta.formatted_starts_at_date + '</td>' +
        '<td>' + date.meta.formatted_starts_at_time + '</td>' +
        '<td>' + date.meta.venue_name + '</td>' +
        (date.meta.ticketteer_link ? ticketteerLinkCell : '<td></td>') +
        '<td>' + premiereText + ' ' + cancelledText + ' ' + date.meta.note.substr(0, 25) + '</td>' +
        '<td>' +
          '<a class="event-creator-edit-date-link" data-id="' + date.ID + '">' +
            $('#event-creator-dates-list-tbody').attr('data-i18n-edit') +
          '</a>' +
          ' | ' +
          '<a class="event-creator-delete-date-link" data-id="' + date.ID + '">' +
            $('#event-creator-dates-list-tbody').attr('data-i18n-delete') +
          '</a>' +
        '</td>' +
      '</tr>';
    $oldRow = $('#event-creator-dates-list-tbody [data-date-id='+date.ID+']');
    if ($oldRow.length > 0){
      $oldRow.after(rowHtml);
      $oldRow.remove();
    } else {
      $('#event-creator-dates-list-tbody').append(rowHtml);
    }
  }

  /**
   * ajax call to ad a new Date (post type)
   */
  function ajaxSaveDate(){
    var nonce = $(this).attr('data-nonce');
    var postId = $(this).attr('data-post-id');
    var dateId = $(this).attr('data-date-id');
    $.post( ajaxurl, {
        'action': 'event_creator_create_date',
        'nonce': nonce,
        'event_id': postId,
        'date_id': dateId,
        'meta':   {
          starts_at_date: $('#event-creator-date-starts_at_date').val(),
          starts_at_time: $('#event-creator-date-starts_at_time').val(),
          note: $('#event-creator-date-note').val(),
          premiere: $('#event-creator-date-premiere').is(':checked') ? '1' : '0',
          cancelled: $('#event-creator-date-cancelled').is(':checked') ? '1' : '0',
          venue_id: $('#event-creator-date-venue_id').val(),
        }
    })
    .done(function(response){
      $('.notifications-wrap:visible').html('');
      addDateRow(response.data);
      $('#event-creator-date-form').hide();
      $('#event-creator-dates-list').show();
      if (response.data.errors) notifyTT(response.data.errors, 'error');
      if (response.data.info) notifyTT(response.data.info, 'info');
    })
    .fail(function(error){
      notify(error.responseText, 'error');
    });
  }

  /**
   * notify user via wordpress classes and prepend ticketteer logo
   *
   * @param  {string} msg  the actual message
   * @param  {string} type error|info
   *
   */
  function notifyTT(msg, type) {
    var prefix = '<span class="ticketteer-text-logo">Ticke<span class="ticketteer-tt">tt</span>eer</span> &nbsp;';
    notify(prefix + msg, type);
  }

  /**
   * notify user via wordpress classes
   *
   * @param  {string} msg  the message
   * @param  {string} type error|info
   *
   */
  function notify(msg, type){
    var classes = 'updated is-dismissible ';
    classes += (type === 'error') ? 'error' : 'notice notice-success';
    $('.wp-header-end')
      .after('<div class="' + classes + '"><p>' + msg + '</p></div>');
  }

}(jQuery));
