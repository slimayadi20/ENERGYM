/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.entities;

/**
 *
 * @author MSI
 */
public class CategorieEvent {
    float id ; 
String nomCategorie ;

    public CategorieEvent(float id, String nomCategorie) {
        this.id = id;
        this.nomCategorie = nomCategorie;
    }

    public CategorieEvent() {
    }

    public CategorieEvent(String nomCategorie) {
        this.nomCategorie = nomCategorie;
    }

    public float getId() {
        return id;
    }

    public void setId(float id) {
        this.id = id;
    }

    public String getNomCategorie() {
        return nomCategorie;
    }

    public void setNomCategorie(String nomCategorie) {
        this.nomCategorie = nomCategorie;
    }


}
