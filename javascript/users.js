const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-list");

searchIcon.onclick = ()=>{
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
}

searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
  }
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/search.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          usersList.innerHTML = data;
        }
    }
  }
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + searchTerm);
}

setInterval(() =>{
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "php/users.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          if(!searchBar.classList.contains("active")){
            usersList.innerHTML = data;
          }
        }
    }
  }
  xhr.send();
}, 500);

// Añadir esta nueva función para actualizar la lista de grupos
function updateGroupsList() {
  let xhr = new XMLHttpRequest();
  xhr.open("GET", "php/get_groups.php", true);
  xhr.onload = ()=>{
    if(xhr.readyState === XMLHttpRequest.DONE){
        if(xhr.status === 200){
          let data = xhr.response;
          document.querySelector(".groups-list").innerHTML = data;
        }
    }
  }
  xhr.send();
}

// Llamar a la función cada 5 segundos
setInterval(updateGroupsList, 5000);

// Agregar event listener para los botones de eliminar grupo
document.addEventListener('click', function(e) {
    if(e.target && e.target.classList.contains('delete-group')) {
        if(confirm('Are you sure you want to delete this group?')) {
            var groupId = e.target.getAttribute('data-group-id');
            deleteGroup(groupId);
        }
    }
});

function deleteGroup(groupId) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/delete_group.php", true);
    xhr.onload = ()=>{
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                if(data === "success"){
                    // Recargar la página o actualizar la lista de grupos
                    location.reload();
                }else{
                    alert(data);
                }
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("group_id="+groupId);
}
