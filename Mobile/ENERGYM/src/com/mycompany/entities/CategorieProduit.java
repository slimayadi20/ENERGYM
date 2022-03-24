/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.entities;

/**
 *
 * @author MSI
 */
public class CategorieProduit {
float id ;  
  String nom ; 

    public CategorieProduit(float id, String nom) {
        this.id = id;
        this.nom = nom;
    }

    public CategorieProduit() {
    }

    public CategorieProduit(String nom) {
        this.nom = nom;
    }

    public float getId() {
        return id;
    }

    public void setId(float id) {
        this.id = id;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }


}
