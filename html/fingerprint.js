import("%INSERT_FP_SCRIPT_FILE%")
	.then(a=>a.load(%INJECT_ENDPOINT_CODE%))
	.then(a=>a.get())
	.then(a=>{
		request_id.value=a.request_id;
		lbutton.disabled=false;
	});