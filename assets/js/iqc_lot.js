$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#iqc-lot').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#iqc-lot').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {
        'data': 'checkbox',
        'orderable': false
      },
      {'data': 'id'},
      {'data': 'number'},
      {'data': 'supplier_name'},
      {'data': 'invoice_number'},
      {'data': 'quantity'},
      {
        'data': 'part',
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

  $("#iqc-lot_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#iqc-lot').DataTable().columns(i).search(v).draw();
  });

  $("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
  });
  $(document).on("click", ".odd, .even", function(event){
    if (event.target.type !== 'checkbox') {
      var obj = $(this).find("input[type=checkbox]");
      var check = obj.is(':checked');
      if(check){
        obj.prop('checked', false);
      } else {
        obj.prop('checked', true);
      }
    }
  });
  $(".btn-delete").click(function(){
    var rows = $('input:checkbox:checked').map(function(){
         return $(this).val();
    }).get();
    if(rows==""){
      alert("Please select at least one record.");
    } else {
      var fullUrl = window.location.href;
      var index = fullUrl.lastIndexOf("/");
      var urlPath = fullUrl.substring(0, index)+"/";
      var url = urlPath+"iqc-lot/delete-multi?rows="+rows;
      location.href = url;
    }
  });

});