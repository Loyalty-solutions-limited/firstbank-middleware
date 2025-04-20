<center style="margin-top:10%"><input type="password" name="pass" placeholder="access key" id="pass" style="padding:10px; width:200px; height:36px;" /> <br><br><button id='proceed' style='padding:10px' onclick="accessPage()">Proceed</button></center>

<script>
    
    function accessPage(){
        let value = document.querySelector("#pass").value;
        var obj = new XMLHttpRequest();
        obj.onreadystatechange = function(){
            if (obj.readyState == 4){
                let res = JSON.parse(obj.responseText)
                window.location = res.url;
            }
        }
        obj.open("POST", "{{url('allow_me')}}", true);
        // Set content type
        obj.setRequestHeader('Content-type', 'application/json; charset=UTF-8');
        // Send the request with data to post
        obj.send(
            JSON.stringify({
                "access":value
            })
        );
    }
    
</script>