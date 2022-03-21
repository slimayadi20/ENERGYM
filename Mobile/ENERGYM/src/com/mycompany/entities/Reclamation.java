/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.entities;

/**
 *
 * @author MSI
 */
public class Reclamation {

    float id;
    String titre;
    String contenu;
    String statut;
    String date;
    float NomUser;

    public Reclamation(String titre, String contenu, float NomUser) {
        this.titre = titre;
        this.contenu = contenu;
        this.NomUser = NomUser;
    }
  

    public float getNomUser() {
        return NomUser;
    }

    public void setNomUser(float NomUser) {
        this.NomUser = NomUser;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getStatut() {
        return statut;
    }

    public void setStatut(String statut) {
        this.statut = statut;
    }

    public Reclamation(float id, String titre, String contenu) {
        this.id = id;
        this.titre = titre;
        this.contenu = contenu;
    }

    public Reclamation(String titre, String contenu) {
        this.titre = titre;
        this.contenu = contenu;
    }

    public Reclamation() {
    }

    public float getId() {
        return id;
    }

    public void setId(float id) {
        this.id = id;
    }

    public String getTitre() {
        return titre;
    }

    public void setTitre(String titre) {
        this.titre = titre;
    }

    public String getContenu() {
        return contenu;
    }

    public void setContenu(String contenu) {
        this.contenu = contenu;
    }

}
