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
let blocHaut = document.getElementById('blocHaut') as HTMLInputElement

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
    } else if (type == "retrait") {

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

// --------------------------------------------------------------------------------------------------------------------------------------
// Interface pour la transaction
interface Transaction {
    montant: string;
    date: string;
  }
  
  // Fonction pour créer la modal
  function createModal(data: any) {
    const faireDepot = data.Fairedepot;
    const receptionViaCode = data.receptionViaCode;
    const receptionClient = data.ReceptionClient;
  
    function createEntry(transaction: Transaction) {
      const entryDiv = document.createElement('div');
      entryDiv.innerHTML = `
        <label class="fs-5">Montant: ${transaction.montant}</label>
        <label class="fs-5">Date: ${transaction.date}</label>
        <hr>
      `;
      return entryDiv;
    }
  
    const modalBody = document.querySelector('.ModalHistorique') as HTMLInputElement;
    modalBody.innerHTML = '';
  
    faireDepot.forEach((transaction: Transaction) => {
      const entry = createEntry(transaction);
      modalBody?.appendChild(entry);
    });
  
    receptionViaCode.forEach((transaction: Transaction) => {
      const entry = createEntry(transaction);
      modalBody?.appendChild(entry);
    });
  
    receptionClient.forEach((transaction: Transaction) => {
      const entry = createEntry(transaction);
      modalBody?.appendChild(entry);
    });
  }
  
  
let afficherHistorique = document.getElementById("afficherHistorique") as HTMLInputElement
let ModalHistorique = document.getElementById("modalHistorique") as HTMLInputElement

transaction.addEventListener('change', () => {
    if (transaction.value == "retrait") {
        blocHaut.classList.add('d-none')
    } else {
        blocHaut.classList.remove('d-none')
    }
})

afficherHistorique.addEventListener('click', () => {

    if (expediteur.value.length == 9) {
        let expdtr = expediteur.value
        let four = fournisseur.value
        if (fournisseur.value) {
            const formData = new FormData();
            formData.append('telephone', expdtr);
            formData.append('fournisseur', four);
            fetch('http://127.0.0.1:8000/api/transaction/user/historique', {
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
                    createModal(data);
                })
                .catch(error => {
                    console.log(error);
                });
        } else {
            const formData = new FormData();
            formData.append('telephone', expdtr);
            fetch('http://127.0.0.1:8000/api/transaction/user/historique', {
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

    }
})