/* très fortement inspiré de http://www.sitepoint.com/html5-ajax-file-upload/ */

(function() {

	// getElementById
	function $id(id) {
		return document.getElementById(id);
	}

  // in_array
  function in_array(element, tableau) {
    for (cle in tableau) {
      if (tableau[cle] == element) { return true; }
      }
      return false;
  }

	// output information
	function Output(msg) {
		var m = $id("messages");
		m.innerHTML = msg;
	}

	// file drag hover
	function FileDragHover(e) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	}

  // traitement de la réponse à la requête xhr
  function GetResponse(xhr) {
    if (xhr.readyState==4 && xhr.status==200)  {
      Output(xhr.responseText);
      console.log('réponse reçue');
      }
  }

  // upload file xhr
  function Upload(file) {
    var xhr = new XMLHttpRequest();
    var max_size = parseInt($id("MAX_FILE_SIZE").value); 
    var extensions = eval($id("extensions").value); 
    var extension = (file.name).split('.').pop();
    if (xhr.upload && file.size <= max_size && in_array(extension, extensions)) {  
      var formData = new FormData();
      formData.append("fichier", file);
      xhr.open("POST", $id("upload").action, true);
      xhr.setRequestHeader("X_FILENAME", file.name);       
      xhr.onreadystatechange = function() { 
                if (xhr.readyState==4 && xhr.status==200  ) { Output(xhr.responseText); } 
                } 
      xhr.send(formData);   
      }
    else { 
      var error_msg = "<p>Ce fichier ne peut pas être envoyé.</p>";
      if (file.size > max_size) { 
        error_msg = "<p>La taille du fichier choisi dépasse la taille autorisée de ";
        error_msg += max_size.toString()+" octets.</p>"; 
        }
      if ( !(in_array(extension, extensions)) ) {
        error_msg = "<p>L'extension du fichier choisi n'est pas autorisée.</p>";
        }
      Output(error_msg);
    }
  }

	// file selection
	function FileSelectHandler(e) {
		// cancel event and hover styling
		FileDragHover(e);
		// fetch FileList object
		var files = e.target.files || e.dataTransfer.files;
    // On récupère le dernier fichier droppé ou sélectionné : on n'en veut qu'un !
    var f;
    f = files[files.length-1];
    Output( "<p>fichier en attente d'envoi : <strong>" + f.name + "</strong></p>"	); 
    Upload(f);
	}


	// initialize
	function Init() {
		var fileselect = $id("fileselect"),
			filedrag = $id("filedrag"),
			submitbutton = $id("submitbutton");
		// file select
		fileselect.addEventListener("change", FileSelectHandler, false);
		// is XHR2 available?
		var xhr = new XMLHttpRequest();
		if (xhr.upload) {
			// file drop
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";
			// remove submit button
			submitbutton.style.display = "none";
		}
	}

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		Init();
	}


})();
