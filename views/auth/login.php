<div class="container">
    <h1>Login</h1>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>Name</label>
                <input id="userName" name="name" type="text" class="form-control">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Password</label>
                <input id="userPassword" name="password" type="password" class="form-control">
            </div>
        </div>
    </div>
    <button id="loginUser" type="button" class="btn btn-primary mt-3">Submit</button>
</div>

<script>
    let btn = document.getElementById('loginUser');

    btn.onclick = () => {

        let formData = new FormData();
        formData.append('name', document.getElementById('userName').value);
        formData.append('password', document.getElementById('userPassword').value);

        axios.post('/login', formData)
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