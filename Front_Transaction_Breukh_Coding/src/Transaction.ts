fetch('http://127.0.0.1:8000/api/user')
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur de requête');
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
        // data.data.forEach(element => {
        //     // console.log(element.libelle);
        //     createOption(element.libelle, element.id)
        // });
    })
    .catch(error => {
        console.error('Une erreur s\'est produite:', error);
    });


let bouton = document.getElementById("envoyer") as HTMLInputElement;
let expediteur = document.getElementById("expediteur") as HTMLInputElement;
let nomComplete = document.getElementById("nomComplete") as HTMLInputElement;
let fournisseur = document.getElementById("fournisseur") as HTMLInputElement;
let transaction = document.getElementById("transaction") as HTMLInputElement;
let destinataire = document.getElementById("destinataire") as HTMLInputElement;
let NomDestinataire = document.getElementById("NomDestinataire") as HTMLInputElement;
let Montant = document.getElementById("Montant") as HTMLInputElement;

fournisseur.addEventListener('change', () => {
    const selectedFournisseur = fournisseur.value;
    if (selectedFournisseur === 'OrangeMoney') {
        document.body.style.backgroundColor = '#FF6600';
    } else if (selectedFournisseur === 'Wave') {
        document.body.style.backgroundColor = '#2E88C7';
    } else if (selectedFournisseur === 'Wari') {
        document.body.style.backgroundColor = '#4FC031';
    } else if (selectedFournisseur === 'Compte Banquaire') {
        document.body.style.backgroundColor = 'gray';
    } else {
        document.body.style.backgroundColor = 'white';
    }
})

expediteur.addEventListener('input', () => {

    if (expediteur.value.length == 9) {
        const formData = new FormData();
        formData.append('telephone', expediteur.value);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('http://127.0.0.1:8000/api/user/charge', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(errors => {
                        console.log(errors);
                    });
                }
            })
            .then(data => {
                console.log(data.nomComplet);
                nomComplete.value = data.nomComplet
            })
            .catch(error => {
                console.log(error);
            });
    }
})

destinataire.addEventListener('input', () => {

    if (destinataire.value.length == 9) {
        const formData = new FormData();
        formData.append('telephone', destinataire.value);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('http://127.0.0.1:8000/api/user/charge', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(errors => {
                        console.log(errors);
                    });
                }
            })
            .then(data => {
                console.log(data.nomComplet);
                NomDestinataire.value = data.nomComplet
            })
            .catch(error => {
                console.log(error);
            });
    }
})

bouton.addEventListener('click', () => {
    let expdtr = expediteur.value
    let dest = destinataire.value
    let four = fournisseur.value
    let type = transaction.value
    let montant = Montant.value
    
    const formData = new FormData();
    formData.append('expediteur', expdtr);
    formData.append('destinataire', dest);
    formData.append('fournisseur', four);
    formData.append('type', type);
    formData.append('montant', montant);
    formData.append('_token', '{{ csrf_token() }}');

    if (type == "depot") {
        fetch('http://127.0.0.1:8000/api/user/1/transaction/depot', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(errors => {
                            console.log(errors);
                        });
                    }
                })
                .then(data => {
                    console.log(data);
                })
                .catch(error => {
                    console.log(error);
                });
    }else if(type == "retrait"){

        fetch('http://127.0.0.1:8000/api/transaction/user/1/retrait', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(errors => {
                            console.log(errors);
                        });
                    }
                })
                .then(data => {
                    console.log(data);
                })
                .catch(error => {
                    console.log(error);
                });
    }

})
