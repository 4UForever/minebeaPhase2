$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#process-log').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'scrollX': true,
    'ajax': {
      'url': $('#process-log').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'desc']],
    'columns': [
      {
        'data': 'checkbox',
        'orderable': false
      },
      {'data': 'id'},
      {'data': 'full_name'},
      {'data': 'line_title'},
      {'data': 'process_number'},
      {'data': 'process_title'},
      {'data': 'product_title'},
      {'data': 'lot_number'},
      {'data': 'line_leader_name'},
      {'data': 'start_time'},
      {'data': 'end_time'},
      {'data': 'total_minute'},
      {'data': 'ok_qty'},
      {'data': 'ng_qty'},
      {'data': 'total_break'},
      {'data': 'first_serial_no'},
      {'data': 'last_serial_no'},
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

  $("#process-log_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#process-log').DataTable().columns(i).search(v).draw();
  });

  $('#complete_filter').on( 'change', function(){
    var v = $(this).val();
    $('#process-log').DataTable().search(v).draw();
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

  $(".export-selected").click(function(){
    var rows = $('input:checkbox:checked').map(function(){
         return $(this).val();
    }).get();
    if(rows==""){
      alert("Please select at least one record.");
    } else {
      var role = $(this).attr('data-role');
      var url = "/admin/process-log/export-"+role+"?rows="+rows;
      location.href = url;
    }
  });

  $("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
  });

});