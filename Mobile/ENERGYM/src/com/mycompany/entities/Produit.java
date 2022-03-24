/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.entities;

/**
 *
 * @author MSI
 */
public class Produit {
    float id ; 
String description ; 
String nom ; 
String image ; 
float prix ; 
float quantite ; 
float categorieid; 

    public float getCategorieid() {
        return categorieid;
    }

    public void setCategorieid(float categorieid) {
        this.categorieid = categorieid;
    }

    public Produit(String description, String nom, String image, float prix, float quantite, float categorieid) {
        this.description = description;
        this.nom = nom;
        this.image = image;
        this.prix = prix;
        this.quantite = quantite;
        this.categorieid = categorieid;
    }

    public Produit(float id, String description, String nom, String image, float prix, float quantite) {
        this.id = id;
        this.description = description;
        this.nom = nom;
        this.image = image;
        this.prix = prix;
        this.quantite = quantite;
    }

    public Produit() {
    }

    public Produit(String description, String nom, String image, float prix, float quantite) {
        this.description = description;
        this.nom = nom;
        this.image = image;
        this.prix = prix;
        this.quantite = quantite;
    }

    public float getId() {
        return id;
    }

    public void setId(float id) {
        this.id = id;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public float getPrix() {
        return prix;
    }

    public void setPrix(float prix) {
        this.prix = prix;
    }

    public float getQuantite() {
        return quantite;
    }

    public void setQuantite(float quantite) {
        this.quantite = quantite;
    }

}
