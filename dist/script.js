function disableSubmit() {
    document.getElementById("submitButton").disabled = true;
  }

  const myForm = document.getElementById('myForm');
  myForm.addEventListener('submit', function() {
    disableSubmit();
  });