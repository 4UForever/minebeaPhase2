



$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Event binding

  $('.model-and-process-form-group').on('change', 'select.document-line-select', function() {
    var self = this;

    $(self).parents('.col-xs-2').siblings('.col-xs-4').children('.form-group').hide();
    $(self).parents('.col-xs-2').siblings('.col-xs-4').children('.ajax-loader').show();
    $('.model-process-add-more').attr('disabled', 'disabled');

    var fullUrl = window.location.href;
    var index = fullUrl.lastIndexOf("/");
    var urlPath = fullUrl.substring(0, index)+"/";
    var url = urlPath+'document/get-model-processes';

    var data = {
      'id': $(self).parents('.row').attr('data-id'),
      'line_id': $(self).val()
    };

    $.post(url, data, function(response) {
      $(self).parents('.col-xs-2').siblings('.document-product-select').html(response.product_select);
      $(self).parents('.col-xs-2').siblings('.document-process-select').html(response.process_select);
      $(self).parents('.col-xs-2').siblings('.col-xs-4').children('.ajax-loader').hide();
      $('.model-process-add-more').removeAttr('disabled', 'disabled');

      var last_selected_model_id = $(self).val();

      var list = $(self);

      /*var previous_line_id = $(self).attr('data-current-select');
      var previous_line_title = $(self).children('option[value="' + previous_line_id + '"]').html();;

      addModelToAllLists(previous_line_id, previous_line_title, self);
      removeModelFromAllLists(last_selected_model_id, list);*/

      chosenRefresh();
    })
    .fail(function(response) {
      alert(response.responseText);
      $(self).parents('.col-xs-2').siblings('.col-xs-4').children('.form-group').show();
      $(self).parents('.col-xs-2').siblings('.col-xs-4').children('.ajax-loader').hide();
      $('.model-process-add-more').removeAttr('disabled', 'disabled');
      chosenRefresh();
    });
  });

  $('.model-and-process-form-group').on('click', '.model-process-add-more', function() {
    var self = this;
    $(self).hide();
    $(self).siblings('.ajax-loader').show();

    var fullUrl = window.location.href;
    var index = fullUrl.lastIndexOf("/");
    var urlPath = fullUrl.substring(0, index)+"/";
    var url = urlPath+'document/get-model-processes-pair';

    $.post(url, function(response) {
      $(self).siblings('.ajax-loader').hide();
      $(self).siblings('.model-process-remove').show();

      $(self).closest('.row').after(response);

      /*var last_selected_model_id = $('select.document-line-select').last().val();

      var list = $('.model-and-process-form-group')
                 .children('.row').last()
                 .children('.col-xs-2')
                 .children('.form-group')
                 .children('select.document-line-select');

      removeModelFromAllLists(last_selected_model_id, list);*/

      $('.chosen-select').chosen();
    })
    .fail(function(response) {
      alert(response.responseText);
      $(self).siblings('.ajax-loader').hide();
      $(self).show();
    });

    return false;
  });

  $('.model-and-process-form-group').on('click', '.model-process-remove', function() {
    /*var list = $(this)
               .closest('.row')
               .children('.col-xs-2')
               .children('.form-group')
               .children('select.document-line-select');

    var remove_model_id = list.val();
    var remove_model_title = list.children('option[value="' + remove_model_id + '"]').html();
    addModelToAllLists(remove_model_id, remove_model_title, list);*/

    $(this).closest('.row').remove();
  });

  // ---------------------------------------------------------------------------
  // Misc

  function getAllSelectedModels() {
    var selected = [];

    $('select.document-line-select').each(function(index) {
      var value = $(this).val();
      selected.push(value);
    });

    return selected;
  }

  function removeModelFromAllLists(model_id, list) {
    $('select.document-line-select').not(list).children('option[value="' + model_id + '"]').remove();
    chosenRefresh();
  }

  function addModelToAllLists(model_id, model_title, list) {
    $('select.document-line-select').not(list).each(function(index) {
      var option = $('<option></option>').attr({
        value: model_id
      }).html(model_title);

      $(this).append(option);
    });

    chosenRefresh();
  }

  function chosenRefresh() {
    $('.chosen-select').chosen();
    $('.chosen-select').trigger('chosen:updated');
  }

  // ---------------------------------------------------------------------------
  // Data table

  $('#documents').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#documents').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'title'},
      {
        'data': 'document_category',
        'orderable': false
      },
      {
        'data': 'bindings',
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

});