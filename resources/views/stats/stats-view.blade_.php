<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        select{padding:10px; border:1px solid black; width:100px !important;  height:36px}
        body{ 
            padding:10px;
        }
    </style>
</head>


<table style="border:1px solid black;" >
    <caption><h3>Enrollment Report</h3></caption>
    <tr>
        <th>Successful Enrollment</th>
        <th>Pending Enrollment</th>
        <th>Failed Enrollment</th>
        <th>Unique Customer Count</th>
    </tr>
    
    <tr>
        <td>{{ $successful_enrollments}} </td>
         <td>{{ $pending_enrollments}} </td> 
          <td>{{ $failed_enrollments}} </td> 
          <td>{{ count($unique_customer_count) }}</td>
    </tr>
    
</table>
<br><hr>


@php
    $count=1;
    $count2 = 1;
    $enrol_paginate = ceil($enrollments/200);
    $trans_paginate = ceil($transactions/200);
    $enrol_report_paginate = ceil(($reports2_count)/100);
@endphp
<table style="border:1px solid black;" class="table table-bordered table-striped table-responsive" id="customers_tbl">
    <thead>
          <tr>
        <th>#</th>
        <th>First name</th>
        <th>Last name</th>
        <th>Email</th>
        <th>Loyalty no</th>
        <th>Member ref</th>
        <th>Enrollment status</th>
    </tr>
    </thead>
     <tbody>
   
@foreach($enrolment_data as $ed)
    
  
         <tr>
        <td>{{$count++}}</td>
        <td>{{$ed->first_name?$ed->first_name:''}}</td>
        <td>{{$ed->last_name?$ed->last_name:''}}</td>
        <td>{{$ed->email?$ed->email:''}}</td>
        <td>{{$ed->loyalty_number?$ed->loyalty_number:''}}</td>
        <td>{{$ed->member_reference?$ed->member_reference:''}}</td>
        <td>{{ $ed->enrollment_status }}</td>
    </tr>
  
 

@endforeach
 </tbody>
</table>
<br>
<select style="" id="customers" onchange="setParam(this.id, 'enrol_offset')">
    <option value=''>..</option>
    @php
for ($i =0; $i < $enrol_paginate; $i++){
$j = $i + 1;
   echo  "<option value='$j'>page " . $j . "</option>";
}
@endphp
</select>


<hr>

<table style="border:1px solid black;" class="table table-bordered table-striped table-responsive" id="enrol_log_offset_tbl" border="1">
    <thead>
        
        <tr>
        <th>#</th>
        <th>First name</th>
        <th>Last name</th>
        <th>Email</th>
        <th>Loyalty no</th>
        <th>Status Code</th>
        <th>Status Message</th>
    </tr>
        
    </thead>
   <tbody>
@foreach($reports2 as $rep2)
    
 
        <tr>
        <td>{{$count2++}}</td>
        <td>{{$rep2->firstname}}</td>
        <td>{{$rep2->lastname}}</td>
        <td>{{$rep2->email?$ed->email:''}}</td>
        <td>{{$rep2->loyalty_number}}</td>
        <td>{{$rep2->status_code}}</td>
        <td>{{ $rep2->status_message }}</td>
    </tr> 
   
  

@endforeach
</tbody>
</table>
<br>
<select style="" id="enrol_log_offset" onchange="setParam(this.id, 'enrol_log_offset')">
    <option value=''>..</option>
    @php
for ($i =0; $i < $enrol_report_paginate; $i++){
$j = $i + 1;
   echo  "<option value='$j'>page " . $j . "</option>";
}
@endphp
</select>


<hr>

   

        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    $('.table').each(function(){
      
        $(this).DataTable( {
        dom: 'Bfrtips',
        paging: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
        
    } )
    })
    
   
    
} );



function setParam(id, key){
  let param = document.querySelector("#"+id).value
  let url = 'https://perx3fidelity.com/middleware/perx_middleware/public/run-stats?' + key + '='+param + "#" + id + "_tbl";
  console.log(url)
  if (url){
  window.location = url;
  }else{
      alert('select a page')
  }
}
</script>


