<?php
$retval=null;
$output=null;
$days=null;
$country=null;
$cmd = "flock run.lock ./run '{$_POST["domain"]}' '{$_POST["country"]}' '{$_POST["org"]}' '{$_POST["orgunit"]}' '{$_POST["days"]}'";
exec($cmd, $output, $retval);
exec("openssl x509 -in fullchain.crt -text -noout", $output2);
?>

<!DOCTYPE html>
<html>
<head>
<style>
	html, body {
		display: border-box;
		background: lightgrey;
		padding: 0 1rem 0 1rem;
		margin: 0;
	}
	h1 { text-align: center; }
	.pem {
		color: lightgreen;
		background-color: black;
		border-radius: 1rem;
		margin: 0;
		padding: 1rem;
	}
	h2, h1, pre, form, code, div, p, input, label, button, #one, #two, #three, #four, #pem {
		margin: 0;
		padding: 0;
	}
	.box {
		display: grid;
		margin-top: 0;
		padding: 0;
		gap: 0.5rem;
		height: auto;
		width: auto;
	}	
	#one {
	 	grid-column-start: 1;
		grid-column-end: 1;
		grid-row-start: 3;
  		grid-row-end: 3;
	}
	#two {
	 	grid-column-start: 2;
		grid-column-end: 2;
		grid-row-start: 2;
  		grid-row-end: 2;
	}
	#three {
	 	grid-column-start: 1;
		grid-column-end: 1;
		grid-row-start: 2;
  		grid-row-end: 2;
	}
	#four {
	 	grid-column-start: 2;
		grid-column-end: 2;
		grid-row-start: 3;
  		grid-row-end: 3;
	}
</style>
</head>

<body>
<pre>
<h1>ECC TLS GENERATOR</h1>
<h2>How to use</h2>
<p>In most cases you will only ever need the certificate fullchain, which consist of the leaf certificate (your certificate) 
and the intermediate certificate(s) (the certificate which signed your leaf certificate), in that order (order is important, 
it should always serve them, from the top; leaf, intermediate(s) and lastly root at the end). You will also need the 
certificate key for your leaf certificate to validate. And lastly the machine you're installing the certificate on will 
need to know about the root certificate authority (CA) (which is the one that signed the intermediate certificate(s)). 
Some clients also let you directly refrence the CA certifcate in their configurations, 
meaning you don't need to install it machine-wide. Useful if you don't have root/sudo/admin access.

Installing the self-signed root CA on Ubuntu 24.04:
	1. Copy the root CA certificate
	2. Create a file at /usr/local/share/ca-certificates
	3. Paste the root CA certificate into that file
	4. Run 'update-ca-certificates'
	
	Or run: <span class="copy" style="border-radius: .5rem; color: lightgreen; background-color: black; padding: .2rem">sudo wget "https://ssl.snus.party/caroot.crt" -P /usr/local/share/ca-certificates --no-check-certificate && sudo update-ca-certificates</span>

	The root CA is now installed on your machine and most clients will 
	look for it for reference when validating certificates signed by it. 
	Clients mostly look in /etc/ssl/certs/ca-certificates.crt which now should have 
	your certificate added at the end

Installing the self-signed root CA on Apple Mac M1:
	1. Download the root CA certificate (<a href="https://ssl.snus.party/caroot.crt#Root CA Certificate">download link</a>)
	2. Open up the Keychain Access app
	3. Go to System -> Certificates and drag the downloaded certificate over
	4. Double click on the newly added certificate (system password is needed)
	5. Under Trust, enable Always Trust on everything

	Refresh this page to see that the site is secured, and the 
	"this site is insecure" message have disappeared in your browser

Installing on Windows?
I don't know, and I feel sorry for you

P.S. You can create a multidomain certificate for several domains/IPs at once, just seperate them by comma. 
The first domain/IP in the list will be your main CN (Common Name) domain.
Example: example.com,www.example.com,1.2.3.4,::ffff:0102:0304

P.S.S. Wildcard certificates are also allowed.
Example: *.example.com

P.S.S.S. Click on the certificates to copy to clipboard

P.S.S.S.S. All certificates are generated as ECC (<a href="https://en.wikipedia.org/wiki/Elliptic-curve_cryptography">Elliptic Curve Cryptography</a>) certificates, 
which is the modern, faster and more secure method than the older RSA version.
</p>
</pre>
<code><form action="/" method="POST">
	<label for="country">Country Code:</label>
	<input id="country" name="country" placeholder="US" type="text" maxlength=2>

	<label for="org">Organization Name:</label>
	<input id="org" name="org" placeholder="Example Company" type="text">

	<label for="orgunit">Organization Unit:</label>
	<input id="orgunit" name="orgunit" placeholder="Example Unit" type="text">

	<label for="days">Days Valid:</label>
	<input id="days" name="days" placeholder="365" type="number">

	<label for="domain">Domain:</label>
	<input id="domain" name="domain" placeholder="localhost,127.0.0.1,::1" type="text" required>
	<button id="button">Generate</button>
</form></code>
<pre class="box">
<div id="one">
	<a href='fullchain.crt'><h1>Certificate Fullchain:</h1></a><div class="pem copy"><?= file_get_contents("fullchain.crt")?></div>
</div>

<div id="two">
	<a href='cert.key'><h1>Certificate Key:</h1></a><div class="pem copy"><?= file_get_contents("cert.key")?></div>
</div>

<div id="three">
	<a href='caroot.crt'><h1>Root CA Certificate:</h1><a/><div class="pem copy"><?= file_get_contents("caroot.crt")?></div>
</div>

<div id="four">
	<a href=''><h1>Certificate Overview:</h1></a><div class="pem copy"><?php print_r($output2) ?></div>
</div>

</pre>
<pre>
<div class="alert" hidden>
<h1><strong style="position: fixed; top: 40%; left: 40%; z-index: 99; color: red; font-size: 50px"><em>Copied!</em></strong></h1>
</pre>
</div>
</body>
</html>

<script>
const sleep = ms => new Promise(r => setTimeout(r, ms));
document.addEventListener('DOMContentLoaded', (event) => {
    document.body.addEventListener('click', (event) => {
        if (event.target.classList.contains('copy')) {
            const textToCopy = event.target.textContent || event.target.innerText;
            navigator.clipboard.writeText(textToCopy).then(() => {
	    	document.querySelector('.alert').removeAttribute('hidden');
	    	setTimeout(function() {
    			document.querySelector('.alert').setAttribute('hidden', '');
		}, 500);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }
    });
});
</script>
<?php unset($_POST); ?>
