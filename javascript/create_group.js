const form = document.querySelector(".signup form"),
continueBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-text");

form.onsubmit = (e)=>{
    e.preventDefault();
}

continueBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/create_group.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              try {
                  data = JSON.parse(data);
                  if(data.status === "success"){
                      location.href = "group_chat.php?room_id=" + data.group_id;
                  }else{
                      errorText.style.display = "block";
                      errorText.textContent = data;
                  }
              } catch(e) {
                  errorText.style.display = "block";
                  errorText.textContent = data;
              }
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}
