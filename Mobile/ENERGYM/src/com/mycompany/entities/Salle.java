/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.entities;

/**
 *
 * @author nouri
 */
public class Salle {
     private float id;
     private String nom;
     private String adresse;
     private String tel;
     private String mail;
     private String description;
     private String heureo;
     private String heuref;
      private String image;

    public Salle() {
    }

    public Salle(float id, String nom, String adresse, String tel, String mail, String description) {
        this.id = id;
        this.nom = nom;
        this.adresse = adresse;
        this.tel = tel;
        this.mail = mail;
        this.description = description;
       
    }

    public Salle(String nom, String adresse, String tel, String mail, String description) {
        this.nom = nom;
        this.adresse = adresse;
        this.tel = tel;
        this.mail = mail;
        this.description = description;
    }

    public float getId() {
        return id;
    }

    public String getNom() {
        return nom;
    }

    public String getAdresse() {
        return adresse;
    }

 

    public String getTel() {
        return tel;
    }

    public String getMail() {
        return mail;
    }

    public String getDescription() {
        return description;
    }

    public String getHeureo() {
        return heureo;
    }

    public String getHeuref() {
        return heuref;
    }

    public String getImage() {
        return image;
    }

    public void setId(float id) {
        this.id = id;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public void setAdresse(String adresse) {
        this.adresse = adresse;
    }

   
    public void setTel(String tel) {
        this.tel = tel;
    }

    public void setMail(String mail) {
        this.mail = mail;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public void setHeureo(String heureo) {
        this.heureo = heureo;
    }

    public void setHeuref(String heuref) {
        this.heuref = heuref;
    }

    public void setImage(String image) {
        this.image = image;
    }
   
   
   
}