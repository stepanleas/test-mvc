<div class="container">
    <h1>Register</h1>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>Name</label>
                <input id="userName" name="name" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input id="userEmail" name="email" type="email" class="form-control">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Password</label>
                <input id="userPassword" name="password" type="password" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirm password</label>
                <input id="userConfirmPassword" name="confirm_password" type="password" class="form-control">
            </div>
        </div>
    </div>
    <button id="registerUser" type="submit" class="btn btn-primary mt-3">Submit</button>
</div>

<script>
    let btn = document.getElementById('registerUser');

    btn.onclick = () => {

        let formData = new FormData();
        formData.append('name', document.getElementById('userName').value);
        formData.append('email', document.getElementById('userEmail').value);
        formData.append('password', document.getElementById('userPassword').value);
        formData.append('confirm_password', document.getElementById('userConfirmPassword').value);

        axios.post('/register', formData)
        .then((resp) => {
            const data = resp.data;
            // Verify if the validation was successfully or not
            switch (data.type) {
                case 'danger':
                    alert(data.message);
                        break;
                case 'success':
                    window.location.replace('/');
                        break;
            }

        }).catch((e) => {
            console.log(e);
        })
    }
</script>