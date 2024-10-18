const form = document.querySelector(".signup form"),
continueBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-text");

form.onsubmit = (e)=>{
    e.preventDefault();
}

continueBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/update_group_settings.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              if(data === "success"){
                  location.href = "group_chat.php?room_id=" + new URLSearchParams(window.location.search).get('group_id');
              }else{
                  errorText.style.display = "block";
                  errorText.textContent = data;
              }
          }
      }
    }
    let formData = new FormData(form);
    formData.append('group_id', new URLSearchParams(window.location.search).get('group_id'));
    xhr.send(formData);
}
