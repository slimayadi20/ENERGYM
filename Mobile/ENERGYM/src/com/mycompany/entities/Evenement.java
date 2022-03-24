/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.entities;

/**
 *
 * @author MSI
 */
public class Evenement {

    float id;
    String nomEvent;
    String DateEvent;
    String DescriptionEvent;
    String LieuEvent;
    String image;
    String Etat;
    float NbrParticipantsEvent;
    float NomCategorie;

    public float getId() {
        return id;
    }

    public void setId(float id) {
        this.id = id;
    }

    public String getNomEvent() {
        return nomEvent;
    }

    public void setNomEvent(String nomEvent) {
        this.nomEvent = nomEvent;
    }

    public String getDateEvent() {
        return DateEvent;
    }

    public void setDateEvent(String DateEvent) {
        this.DateEvent = DateEvent;
    }

    public String getDescriptionEvent() {
        return DescriptionEvent;
    }

    public void setDescriptionEvent(String DescriptionEvent) {
        this.DescriptionEvent = DescriptionEvent;
    }

    public String getLieuEvent() {
        return LieuEvent;
    }

    public void setLieuEvent(String LieuEvent) {
        this.LieuEvent = LieuEvent;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public String getEtat() {
        return Etat;
    }

    public void setEtat(String Etat) {
        this.Etat = Etat;
    }

    public float getNbrParticipantsEvent() {
        return NbrParticipantsEvent;
    }

    public void setNbrParticipantsEvent(float NbrParticipantsEvent) {
        this.NbrParticipantsEvent = NbrParticipantsEvent;
    }

    public float getNomCategorie() {
        return NomCategorie;
    }

    public void setNomCategorie(float NomCategorie) {
        this.NomCategorie = NomCategorie;
    }

    public Evenement(String nomEvent, String DateEvent, String DescriptionEvent, String LieuEvent, String image, String Etat, float NbrParticipantsEvent, float NomCategorie) {
        this.nomEvent = nomEvent;
        this.DateEvent = DateEvent;
        this.DescriptionEvent = DescriptionEvent;
        this.LieuEvent = LieuEvent;
        this.image = image;
        this.Etat = Etat;
        this.NbrParticipantsEvent = NbrParticipantsEvent;
        this.NomCategorie = NomCategorie;
    }

    public Evenement(float id, String nomEvent, String DateEvent, String DescriptionEvent, String LieuEvent, String image, String Etat, float NbrParticipantsEvent, float NomCategorie) {
        this.id = id;
        this.nomEvent = nomEvent;
        this.DateEvent = DateEvent;
        this.DescriptionEvent = DescriptionEvent;
        this.LieuEvent = LieuEvent;
        this.image = image;
        this.Etat = Etat;
        this.NbrParticipantsEvent = NbrParticipantsEvent;
        this.NomCategorie = NomCategorie;
    }

    public Evenement() {
    }

}
