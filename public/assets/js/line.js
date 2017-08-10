



$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#lines').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#lines').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'title'},
      {
        'data': 'products',
        'orderable': false
      },
      {
        'data': 'processes',
        'orderable': false
      },
      {'data': 'created_at'},
      {'data': 'updated_at'},
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

  // ---------------------------------------------------------------------------
  // Process Forms

  $('#new-processes .btn-warning').colorbox({
    'width': '30%',
    'onComplete': colorboxUpdateProcessCompleteCallback
  });

  $('#new-processes .btn-danger').click(function(event) {
    $(this).parents('td').parents('tr').remove();
    event.preventDefault();
  });

  function colorboxAddNewProcessCompleteCallback() {
    $('#new-process-form').submit(function(event) {
      var inputs = $('#new-process-form :input');

      var data = {};

      inputs.each(function() {
        data[this.name] = $(this).val();
      });

      var url = $('#new-process-form').attr('data-validaton-url');

      $('#new-process-form input[type="submit"]').val('Processing...');
      $('#new-process-form input[type="submit"]').prop('disabled', true);

      $.post(url, data, function(response) {
        $('#new-process-form input[type="submit"]').val('Success');
        $('#new-processes tr.no-content').remove();
        $('#new-processes tbody').append(response);

        $('#new-processes .btn-warning').colorbox({
          'width': '30%',
          'onComplete': colorboxUpdateProcessCompleteCallback
        });

        $('#new-processes .btn-danger').click(function(event) {
          $(this).parents('td').parents('tr').remove();
          event.preventDefault();
        });

        $.colorbox.close()
      })
      .fail(function(response) {
        $('#new-process-form input[type="submit"]').val('SUBMIT');
        $('#new-process-form input[type="submit"]').prop('disabled', false);
        $('#new-process-form').prepend(response.responseJSON);
      });

      event.preventDefault();
    });
  }

  function colorboxUpdateProcessCompleteCallback() {
    var self = this;

    $('#new-process-form').submit(function(event) {
      var inputs = $('#new-process-form :input');

      var data = {};

      inputs.each(function() {
        data[this.name] = $(this).val();
      });

      var url = $('#new-process-form').attr('data-validaton-url');

      $('#new-process-form input[type="submit"]').val('Processing...');
      $('#new-process-form input[type="submit"]').prop('disabled', true);

      $.post(url, data, function(response) {
        $('#new-process-form input[type="submit"]').val('Success');
        $(self).parents('td').parents('tr').replaceWith(response);

        $('#new-processes .btn-warning').colorbox({
          'width': '30%',
          'onComplete': colorboxUpdateProcessCompleteCallback
        });

        $('#new-processes .btn-danger').click(function(event) {
          $(this).parents('td').parents('tr').remove();
          event.preventDefault();
        });

        $.colorbox.close()
      })
      .fail(function(response) {
        $('#new-process-form input[type="submit"]').val('SUBMIT');
        $('#new-process-form input[type="submit"]').prop('disabled', false);
        $('#new-process-form').prepend(response.responseJSON);
      });

      event.preventDefault();
    });
  }

  $('#add-new-process').colorbox({
    'width': '30%',
    'onComplete': colorboxAddNewProcessCompleteCallback
  });

  // ---------------------------------------------------------------------------
  // Product Forms

  $('#new-products .btn-warning').colorbox({
    'width': '30%',
    'onComplete': colorboxUpdateProductCompleteCallback
  });

  $('#new-products .btn-danger').click(function(event) {
    $(this).parents('td').parents('tr').remove();
    event.preventDefault();
  });

  function colorboxAddNewProductCompleteCallback() {
    $('#new-product-form').submit(function(event) {
      var inputs = $('#new-product-form :input');

      var data = {};

      inputs.each(function() {
        data[this.name] = $(this).val();
      });

      var url = $('#new-product-form').attr('data-validaton-url');

      $('#new-product-form input[type="submit"]').val('Processing...');
      $('#new-product-form input[type="submit"]').prop('disabled', true);

      $.post(url, data, function(response) {
        $('#new-product-form input[type="submit"]').val('Success');
        $('#new-products tr.no-content').remove();
        $('#new-products tbody').append(response);

        $('#new-products .btn-warning').colorbox({
          'width': '30%',
          'onComplete': colorboxUpdateProductCompleteCallback
        });

        $('#new-products .btn-danger').click(function(event) {
          $(this).parents('td').parents('tr').remove();
          event.preventDefault();
        });

        $.colorbox.close()
      })
      .fail(function(response) {
        $('#new-product-form input[type="submit"]').val('SUBMIT');
        $('#new-product-form input[type="submit"]').prop('disabled', false);
        $('#new-product-form').prepend(response.responseJSON);
      });

      event.preventDefault();
    });
  }

  function colorboxUpdateProductCompleteCallback() {
    var self = this;

    $('#new-product-form').submit(function(event) {
      var inputs = $('#new-product-form :input');

      var data = {};

      inputs.each(function() {
        data[this.name] = $(this).val();
      });

      var url = $('#new-product-form').attr('data-validaton-url');

      $('#new-product-form input[type="submit"]').val('Processing...');
      $('#new-product-form input[type="submit"]').prop('disabled', true);

      $.post(url, data, function(response) {
        $('#new-product-form input[type="submit"]').val('Success');
        $(self).parents('td').parents('tr').replaceWith(response);

        $('#new-products .btn-warning').colorbox({
          'width': '30%',
          'onComplete': colorboxUpdateProductCompleteCallback
        });

        $('#new-products .btn-danger').click(function(event) {
          $(this).parents('td').parents('tr').remove();
          event.preventDefault();
        });

        $.colorbox.close()
      })
      .fail(function(response) {
        $('#new-product-form input[type="submit"]').val('SUBMIT');
        $('#new-product-form input[type="submit"]').prop('disabled', false);
        $('#new-product-form').prepend(response.responseJSON);
      });

      event.preventDefault();
    });
  }

  $('#add-new-product').colorbox({
    'width': '30%',
    'onComplete': colorboxAddNewProductCompleteCallback
  });

});