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
public class Cours {
     private float id;
     private String nom;
     private String nomCoach;
     private String description;
     private String nombre;
     private String image;
     private String heureD;
     private String heureF;
     private String salleassocie;
     private String jour;

    public Cours() {
    }

    public Cours(float id, String nom, String nomCoach, String description, String nombre, String jour) {
        this.id = id;
        this.nom = nom;
        this.nomCoach = nomCoach;
        this.description = description;
        this.nombre = nombre;
        this.jour = jour;
    }

    public Cours(String nom, String nomCoach, String description, String nombre, String salleassocie, String jour) {
        this.nom = nom;
        this.nomCoach = nomCoach;
        this.description = description;
        this.nombre = nombre;
        this.salleassocie = salleassocie;
        this.jour = jour;
    }
   

    public float getId() {
        return id;
    }

    public String getNom() {
        return nom;
    }

    public String getNomCoach() {
        return nomCoach;
    }

    public String getDescription() {
        return description;
    }

    public String getNombre() {
        return nombre;
    }

    public String getImage() {
        return image;
    }

    public String getHeureD() {
        return heureD;
    }

    public String getHeureF() {
        return heureF;
    }

    public String getSalleassocie() {
        return salleassocie;
    }

    public String getJour() {
        return jour;
    }

    public void setId(float id) {
        this.id = id;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public void setNomCoach(String nomCoach) {
        this.nomCoach = nomCoach;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public void setHeureD(String heureD) {
        this.heureD = heureD;
    }

    public void setHeureF(String heureF) {
        this.heureF = heureF;
    }

    public void setSalleassocie(String salleassocie) {
        this.salleassocie = salleassocie;
    }

    public void setJour(String jour) {
        this.jour = jour;
    }
   
     
}